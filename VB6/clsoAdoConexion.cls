VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsoAdoConexion"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private msCadenaConexion As String
Private msProveedor As String
'----------------------------------------------------------------------
'-----   Empleada para comparar los estados de los objetos        -----
'-----   Donde:                                                   -----
'-----            adStateClosed     ---> 0 (cerrado)              -----
'-----            adStateOpen       ---> 1 (abierto)              -----
'-----            adStateConnecting ---> 2 (conectando)           -----
'-----            adStateExecuting  ---> 4 (ejecutando comando)   -----
'-----            asStateFetching   ---> 8 (Obteniendo regs.)     -----
'----------------------------------------------------------------------
Private meEstadoConexion As ObjectStateEnum
Private mePropiedades As Properties
Private oAdoCnn1 As ADODB.Connection

Public Property Get CadenaConexion() As String
    CadenaConexion = msCadenaConexion
End Property

Public Property Let CadenaConexion(psValor As String)
'    CadenaConexion = psValor
    msCadenaConexion = psValor
    oAdoCnn1.ConnectionString = psValor
End Property

Public Property Get Proveedor() As String
    Proveedor = msProveedor
End Property

Public Property Let Proveedor(psValor As String)
'    Proveedor = psValor
    msProveedor = psValor
    oAdoCnn1.Provider = psValor
End Property

Public Property Get Propiedades() As Properties
    'Propiedades = mePropiedades
    Set Propiedades = oAdoCnn1.Properties
End Property

Public Property Get Estado() As ObjectStateEnum
    Estado = meEstadoConexion
End Property

Public Sub Abrir(Optional psCadenaConexion As String = "", Optional psUsuario As String = "", Optional ByVal psPassword As String = "", Optional ByVal peOpciones As ConnectOptionEnum = adAsyncConnect)
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If IsNull(psCadenaConexion) Then
        oAdoCnn1.ConnectionString = msCadenaConexion
    Else
        oAdoCnn1.ConnectionString = psCadenaConexion
        msCadenaConexion = psCadenaConexion
    End If

    If IsNull(psUsuario) Then
        meEstadoConexion = adStateClosed
        Exit Sub
    End If

    oAdoCnn1.Open msCadenaConexion, psUsuario, psPassword

    Select Case oAdoCnn1.State
        Case 0      '-----   cerrado              -----
            meEstadoConexion = adStateClosed
        Case 1      '-----   abierto              -----
            meEstadoConexion = adStateOpen
        Case 2      '-----   conectando           -----
            meEstadoConexion = adStateConnecting
        Case 4      '-----   ejecutando comando   -----
            meEstadoConexion = adStateExecuting
        Case 8      '-----   obteniendo regs.     -----
            meEstadoConexion = adStateFetching
    End Select

    Exit Sub
    Screen.MousePointer = vbDefault
RutinaError:
    If (Err.Number = -2147467259) Then
        MsgBox "No se pudo establecer la conexión con la Base de Datos..." & vbNewLine & vbNewLine & "Intentelo nuevamente o consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
    Else
        MensajeError Err
    End If
End Sub

Public Sub Cerrar()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If (oAdoCnn1.State = 1) Then oAdoCnn1.Close
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub LiberarRecurso()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Set oAdoCnn1 = Nothing

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Function Ejecutar(ByVal psCadenaSQL As String) As ADODB.Recordset
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Set Ejecutar = oAdoCnn1.Execute(psCadenaSQL)
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Public Function ObjConexion() As ADODB.Connection
    Set ObjConexion = oAdoCnn1
End Function

Public Function IniciarTrans() As Long
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    IniciarTrans = oAdoCnn1.BeginTrans
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Public Sub DeshacerTrans()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    oAdoCnn1.RollbackTrans

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub AceptarTrans()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    oAdoCnn1.CommitTrans

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Class_Initialize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Set oAdoCnn1 = New ADODB.Connection
    msCadenaConexion = ""
    meEstadoConexion = adStateClosed

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub
