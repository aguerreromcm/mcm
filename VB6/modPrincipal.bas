Attribute VB_Name = "modPrincipal"
Option Explicit

'declaracion de variables para la Administración de la apliación
Public sUsuarioApp As String
Public sPasswordApp As String
Public sMensajeApp As String
Public sAmbiente As String
Public sNomArcExcel As String
'Empleada para controlar la finalización de la aplicación
Public bCerrarApp As Boolean
'Utilizadas para obter los conjunto de registros a partir de una consulta SQL.
Public oAdoRst1 As ADODB.Recordset
Public oAdoRst2 As ADODB.Recordset
Public oAdoRst3 As ADODB.Recordset
Public oAdoRst4 As ADODB.Recordset
Public oAdoRst5 As ADODB.Recordset
'Empleadas para realizar las conexiones
Public oAccesoDatos As New clsoAccesoDatos
'Public oAdoCnn1 As ADODB.Connection
Public oAdoCnn2 As ADODB.Connection
Public oAdoCnn3 As ADODB.Connection
'Utilizadas para construir las consultas SQL
Public sSelectSQL1 As String
Public sSelectSQL2 As String
Public sSelectSQL3 As String
Public sSelectSQL4 As String
Public sSelectSQL5 As String

Public sFromSQL1 As String
Public sFromSQL2 As String
Public sFromSQL3 As String
Public sFromSQL4 As String
Public sFromSQL5 As String

Public sWhereSQL1 As String
Public sWhereSQL2 As String
Public sWhereSQL3 As String
Public sWhereSQL4 As String
Public sWhereSQL5 As String

Public sOrderBySQL1 As String
Public sOrderBySQL2 As String
Public sOrderBySQL3 As String

'Empleadas para querys de la bitácora de importación
Public sImportarSQL As String
Public sImportarDetSQL As String
Public iIdImportacion As Long
Public bImportacion As Boolean
Public iSecuenciaImp As String
Public iContImp As Integer

Public Const TITULO_MENSAJE = "Sistema de administración de microcréditos"
Public Const TITULO_MOD_IMP = "Módulo de importación de pagos"
Public Const TITULO_MOD_DEVGAR = "Módulo de devolución de garantias"
Public Const TITULO_MOD_DEL = "Módulo de eliminación de pagos"
Public Const TITULO_MOD_CON = "Módulo de conciliación de pagos"

Public Const IMP_SIN_FECHA = "No tiene registrada una fecha de pago."
Public Const IMP_SIN_MONTO = "No se registro el monto del pago."
Public Const IMP_SIN_REF = "No se cuenta con la referencia del pago."
Public Const IMP_SIN_COD_GPO = "El código del grupo no existe."
Public Const IMP_SIN_COD_IND = "El código del cliente no existe."
Public Const IMP_SQLBAD_COD = "No fue posible validar el código."
Public Const IMP_COD_NO_IND = "El código no existe."
Public Const IMP_CICLO_INV = "El Ciclo no es válido o su situación no es correcta."
Public Const IMP_SITUACION_LIQ = "El crédito se encuentra liquidado."
Public Const IMP_SQLBAD_CICLO = "No fue posible validar el ciclo."
Public Const IMP_SQLBAD_EXIST = "No fue posible verificar si ya esta registrado este pago en la B.D."
Public Const IMP_EXISTE_PAGO_IDEN = "El pago ya existe en la B.D. y tiene status IDENTIFICADO."
Public Const IMP_EXISTE_PAGO_NOIDEN = "El pago ya existe en la B.D. y tiene status NO IDENTIFICADO."
Public Const IMP_MSG_NOIDEN = "No se identificó el pago debido a lo siguiente:"
Public Const IMP_MSG_NOIMP = "No se importó el pago debido a lo siguiente:"

Public Enum eTipoDeDato
    adNumerico = 0
    adAlfanumerico = 1
    adFecha = 2
End Enum

Public Enum eResultadoQuery
    adSinDatos = 0
    adConDatos = 1
    adFalloQuery = 2
End Enum

Public Enum eFormatoFecha
    adYYYYMMDD = 0
    adYYYYMD = 1
    adYYMMDD = 2
    adYYMD = 3
    
    adDDMMYYYY = 4
    adDMYYYY = 5
    adDDMMYY = 6
    adDMYY = 7
    
    adMMDDYYYY = 8
    adMDYYYY = 9
    adMMDDYY = 10
    adMDYY = 11
End Enum

Public Enum eSeparadorFec
    adGuion = 0
    adDiagonal = 1
End Enum

Public Enum eLado
    adDerecha = 0
    adIzquierda = 1
End Enum

Public Enum eTipoValidacion
    adIdentificado = 0
    adNoIdentificado = 1
    adNoImportado = 2
    adArqueoDeCaja = 3
    adNoValidado = 4
    adCancelarImp = 5
End Enum

Public sFechaImp As String, dMontoPago As Double, sRefIM As String, sFechaCarga As String
Public cPagoImp As New clsoPagoImp
Public cCtaBancaria As New clsoCtaBancaria
Public sEmpresa As String, fdPago As Date, sCantidad As String, sCtaBco As String, bIdentifica As Boolean, sSecuencia As String, bResIdenPago As Boolean
'Variables para las polizas contables AMGM 12MAY2010
Public nPoliza As Integer
    
Public Function ValidarNulos(ByVal pvDato As Variant, ByVal peTipoDato As eTipoDeDato) As String
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Select Case peTipoDato
        Case 0      '-----   Dato Numérico                      -----
            ValidarNulos = IIf(IsNull(pvDato), 0, pvDato)
        Case 1, 2      '-----   Dato Alfanumérico, Dato Fecha   -----
            ValidarNulos = IIf(IsNull(pvDato), "", pvDato)
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Public Function DarFormatoCortoAFecha(ByVal psFecha As String, ByVal peFormato As eFormatoFecha, ByVal peSeparador As eSeparadorFec) As String
    Dim sAnio As String, sMes As String, sDia As String, sSeparador As String
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    If (peSeparador = adDiagonal) Then sSeparador = "/" Else sSeparador = "-"
    
    If (IsDate(psFecha)) Then
        sAnio = CStr(Year(psFecha))
        sMes = CStr(Month(psFecha))
        sDia = CStr(Day(psFecha))
        
        Select Case peFormato
            Case 0, 4, 8  '-----   YYYYMMDD, DDMMYYYY, MMDDYYYY   -----
                If (Len(sAnio) = 2) Then sAnio = "20" & sAnio
                If (Len(sMes) = 1) Then sMes = "0" & sMes
                If (Len(sDia) = 1) Then sDia = "0" & sDia
            Case 1, 5, 9  '-----   YYYYMD, DMYYYY, MDYYYY         -----
                If (Len(sAnio) = 2) Then sAnio = "20" & sAnio
                sMes = CStr(Val(sMes))
                sDia = CStr(Val(sDia))
            Case 2, 6, 10 '-----   YYMMDD, DDMMYY, MMDDYY         -----
                If (Len(sAnio) = 4) Then sAnio = Mid(sAnio, 3, 2)
                If (Len(sMes) = 1) Then sMes = "0" & sMes
                If (Len(sDia) = 1) Then sDia = "0" & sDia
            Case 3, 7, 11 '-----   YYMD, DMYY, MDYY               -----
                If (Len(sAnio) = 4) Then sAnio = Mid(sAnio, 3, 2)
                sMes = CStr(Val(sMes))
                sDia = CStr(Val(sDia))
        End Select
        
        Select Case peFormato
            Case 0, 1, 2, 3     '-----   Año, Mes, Dia   -----
                DarFormatoCortoAFecha = sAnio & sSeparador & sMes & sSeparador & sDia
            Case 4, 5, 6, 7     '-----   Dia, Mes, Año   -----
                DarFormatoCortoAFecha = sDia & sSeparador & sMes & sSeparador & sAnio
            Case 8, 9, 10, 11   '-----   Mes, Dia, Año   -----
                DarFormatoCortoAFecha = sMes & sSeparador & sDia & sSeparador & sAnio
        End Select
    Else
        DarFormatoCortoAFecha = ""
    End If
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Public Function ConcatenarCaracter(ByVal psCadena As String, ByVal peLado As eLado, ByVal lLongCadena As Long, ByVal psCaracter As String) As String
    Dim lContador As Long
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    If Len(psCadena) < lLongCadena Then
        Select Case peLado
            Case 0  'Derecha
                For lContador = Len(psCadena) To lLongCadena
                    psCadena = psCadena & psCaracter
                Next
            Case 1  'Izquierda
                For lContador = Len(psCadena) To lLongCadena
                    psCadena = psCaracter & psCadena
                Next
        End Select
    End If
    
    ConcatenarCaracter = psCadena
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Public Sub Main()
    frmInicioSesion.Show
End Sub

Public Sub MensajeError(ByVal poError As Object)
    Screen.MousePointer = vbHourglass
    
    MsgBox "Se ha generado el Error no: " & CStr(poError.Number) & vbNewLine & "Descripción: " & poError.Description, vbCritical + vbOKOnly, TITULO_MENSAJE
    Screen.MousePointer = vbDefault
End Sub

Public Function ValidaAcceso(usuario As String, id_menu As Integer) As Integer
    Dim sImportarSQL As String
    Dim oRstPago As New clsoAdoRecordset
    
    On Error GoTo RutinaError

        ValidaAcceso = 0

        sImportarSQL = "SELECT COUNT(*) VALOR "
        sImportarSQL = sImportarSQL & "FROM SEG_APP_PAG_GAR "
        sImportarSQL = sImportarSQL & "WHERE CDGEM = 'EMPFIN' "
        sImportarSQL = sImportarSQL & "AND CDGPE = '" & usuario & "' "
        sImportarSQL = sImportarSQL & "AND ID_MENU = " & id_menu & " "

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0 '-----   La consulta no retorno registros.   -----
                    'MsgBox "Este usuario no tiene privilegios para esta transacción. Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oRstPago.Cerrar
                    Screen.MousePointer = vbDefault
            Case 1 '-----   Hay registros.                       -----
            
                If oRstPago.ObjSetRegistros.Fields("valor").Value = 1 Then
                    ValidaAcceso = 1
                End If
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "La aplicación no pudo consultar los permisos del usuario." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
        End Select
        
        oRstPago.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function
Public Function ValidaUsuarioPagos(usuario As String) As Integer
    Dim sImportarSQL As String
    Dim oRstPago As New clsoAdoRecordset
    
    On Error GoTo RutinaError

        ValidaUsuarioPagos = 0

        sImportarSQL = "SELECT fnValidaCtaPagos('EMPFIN','" & usuario & "') VALOR FROM DUAL"

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0 '-----   La consulta no retorno registros.   -----
                    'MsgBox "Este usuario no tiene privilegios para esta transacción. Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oRstPago.Cerrar
                    Screen.MousePointer = vbDefault
            Case 1 '-----   Hay registros.                       -----
            
                If oRstPago.ObjSetRegistros.Fields("valor").Value = 1 Then
                    ValidaUsuarioPagos = 1
                End If
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "La aplicación no pudo consultar los permisos del usuario." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
        End Select
        
        oRstPago.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function
