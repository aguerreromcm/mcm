VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsoAdoRecordset"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

'----------------------------------------------------------------------
'-----   Empleada para comparar los estados de los objetos        -----
'-----   Donde:                                                   -----
'-----            adStateClosed     ---> 0 (cerrado)              -----
'-----            adStateOpen       ---> 1 (abierto)              -----
'-----            adStateConnecting ---> 2 (conectando)           -----
'-----            adStateExecuting  ---> 4 (ejecutando comando)   -----
'-----            asStateFetching   ---> 8 (Obteniendo regs.)     -----
'----------------------------------------------------------------------
Private moNumeroRegistros As ADO_LONGPTR
Private meEstadoRecordset As ObjectStateEnum
Private mbFinDeArchivo As Boolean
Private mbInicioDeArchivo As Boolean
Private oAdoRst1 As ADODB.Recordset

Public Property Get Estado() As ObjectStateEnum
    Estado = meEstadoRecordset
End Property

Public Property Get NumeroRegistros() As ADO_LONGPTR
    NumeroRegistros = oAdoRst1.RecordCount
End Property

Public Property Get FinDeArchivo() As Boolean
    FinDeArchivo = mbFinDeArchivo
End Property

Public Property Get InicioDeArchivo() As Boolean
    InicioDeArchivo = mbInicioDeArchivo
End Property

Public Property Get HayRegistros() As eResultadoQuery
'Public Property Get HayRegistros() As Integer
    '-----   No hay registros   -----
    If (oAdoRst1.State = 1 And oAdoRst1.EOF And oAdoRst1.BOF) Then
        HayRegistros = adSinDatos
        moNumeroRegistros = 0
        'HayRegistros = 0
    '-----   Estamos en el primer registros   -----
    ElseIf (oAdoRst1.State = 1 And Not oAdoRst1.EOF And oAdoRst1.BOF) Then
        HayRegistros = adConDatos
        moNumeroRegistros = oAdoRst1.RecordCount
        'HayRegistros = 1
    '-----   Estamos en el último registro   -----
    ElseIf (oAdoRst1.State = 1 And oAdoRst1.EOF And Not oAdoRst1.BOF) Then
        HayRegistros = adConDatos
        moNumeroRegistros = oAdoRst1.RecordCount
        'HayRegistros = 1
    '-----   Hay registros y estamos entre el primero y el último   -----
    ElseIf (oAdoRst1.State = 1 And Not oAdoRst1.EOF And Not oAdoRst1.BOF) Then
        HayRegistros = adConDatos
        moNumeroRegistros = oAdoRst1.RecordCount
        'HayRegistros = 1
    '-----   El Recordset esta cerrado   -----
    ElseIf (oAdoRst1.State <> 1) Then
        HayRegistros = adFalloQuery
        moNumeroRegistros = 0
        'HayRegistros = 2
    '-----   De otra forma   ------
    Else
        HayRegistros = adFalloQuery
        moNumeroRegistros = 0
        'HayRegistros = 2
    End If
End Property

Public Sub Abrir(Optional ByVal psCadenaSQL As String, Optional ByVal poConexion As Object, Optional ByVal peTipoCursor As CursorTypeEnum = -1, Optional ByVal peTipoCerrojo As LockTypeEnum = -1, Optional ByVal plOpciones As Long = -1)
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    'Set oAdoRst1 = New ADODB.Recordset
    
    oAdoRst1.Open psCadenaSQL, poConexion, peTipoCursor, peTipoCerrojo, plOpciones
    
    Select Case oAdoRst1.State
        Case 0      '-----   cerrado              -----
            meEstadoRecordset = adStateClosed
            mbFinDeArchivo = True
            mbInicioDeArchivo = True
            moNumeroRegistros = 0
        Case 1      '-----   abierto              -----
            meEstadoRecordset = adStateOpen
            mbFinDeArchivo = oAdoRst1.EOF
            mbInicioDeArchivo = oAdoRst1.BOF
            moNumeroRegistros = oAdoRst1.RecordCount
        Case 2      '-----   conectando           -----
            meEstadoRecordset = adStateConnecting
        Case 4      '-----   ejecutando comando   -----
            meEstadoRecordset = adStateExecuting
        Case 8      '-----   obteniendo regs.     -----
            meEstadoRecordset = adStateFetching
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub Cerrar()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If oAdoRst1.State = 1 Then
        oAdoRst1.Close
        meEstadoRecordset = adStateClosed
        mbInicioDeArchivo = True
        mbFinDeArchivo = True
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub LiberarRecurso()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Set oAdoRst1 = Nothing
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub


Public Sub IrAlRegAnterior()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If oAdoRst1.State = 1 And Not oAdoRst1.BOF Then
        oAdoRst1.MovePrevious
        If (oAdoRst1.BOF) Then mbInicioDeArchivo = True
        If (Not oAdoRst1.EOF) Then mbFinDeArchivo = False
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub IrAlRegSiguiente()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If oAdoRst1.State = 1 And Not oAdoRst1.EOF Then
        oAdoRst1.MoveNext
        If (oAdoRst1.EOF) Then mbFinDeArchivo = True
        If (Not oAdoRst1.BOF) Then mbInicioDeArchivo = False
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub IrAlPrimerReg()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If oAdoRst1.State = 1 And Not oAdoRst1.BOF Then
        oAdoRst1.MoveFirst
        If (oAdoRst1.BOF) Then mbInicioDeArchivo = True
        If (Not oAdoRst1.EOF) Then mbFinDeArchivo = False
        'moNumeroRegistros = oAdoRst1.RecordCount
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub IrAlUltimoReg()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If oAdoRst1.State = 1 And Not oAdoRst1.EOF Then
        oAdoRst1.MoveLast
        If (oAdoRst1.EOF) Then mbFinDeArchivo = True
        If (Not oAdoRst1.BOF) Then mbInicioDeArchivo = False
        'moNumeroRegistros = oAdoRst1.RecordCount
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Function ObjSetRegistros() As ADODB.Recordset
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Set ObjSetRegistros = oAdoRst1
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Private Sub Class_Initialize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Set oAdoRst1 = New ADODB.Recordset
    mbInicioDeArchivo = True
    mbFinDeArchivo = True
    meEstadoRecordset = adStateClosed

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

