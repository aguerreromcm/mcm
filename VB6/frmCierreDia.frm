VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Object = "{20C62CAE-15DA-101B-B9A8-444553540000}#1.1#0"; "MSMAPI32.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmCierreDia 
   AutoRedraw      =   -1  'True
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Conciliación de Pagos"
   ClientHeight    =   3735
   ClientLeft      =   2340
   ClientTop       =   2115
   ClientWidth     =   9915
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmCierreDia.frx":0000
   LinkTopic       =   "Form1"
   LockControls    =   -1  'True
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3735
   ScaleWidth      =   9915
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9915
      TabIndex        =   6
      Top             =   0
      Width           =   9915
      Begin VB.PictureBox Picture2 
         Height          =   735
         Left            =   360
         Picture         =   "frmCierreDia.frx":08CA
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   14
         Top             =   0
         Width           =   1095
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Cierre de Día"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   14.25
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00FFFFFF&
         Height          =   345
         Left            =   2040
         TabIndex        =   9
         Top             =   60
         Width           =   3495
      End
      Begin VB.Label Label8 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Sistemas 2011"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00FFFFFF&
         Height          =   180
         Left            =   8430
         TabIndex        =   8
         Top             =   180
         Width           =   1170
      End
      Begin VB.Label Label10 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "®"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00FFFFFF&
         Height          =   180
         Left            =   9600
         TabIndex        =   7
         Top             =   90
         Width           =   135
      End
   End
   Begin MSComctlLib.ProgressBar pbarConciliacion 
      Height          =   195
      Left            =   5070
      TabIndex        =   0
      Top             =   8250
      Width           =   1995
      _ExtentX        =   3519
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin VB.PictureBox pbContenido 
      Align           =   1  'Align Top
      BackColor       =   &H00FFFFFF&
      Height          =   2640
      Left            =   0
      ScaleHeight     =   2580
      ScaleWidth      =   9855
      TabIndex        =   2
      Top             =   735
      Width           =   9915
      Begin VB.Frame Frame1 
         Caption         =   "Cierre de Día"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   2040
         Left            =   30
         TabIndex        =   10
         Top             =   20
         Width           =   9840
         Begin MSMAPI.MAPIMessages MAPIMessages1 
            Left            =   7800
            Top             =   1440
            _ExtentX        =   1005
            _ExtentY        =   1005
            _Version        =   393216
            AddressEditFieldCount=   1
            AddressModifiable=   0   'False
            AddressResolveUI=   0   'False
            FetchSorted     =   0   'False
            FetchUnreadOnly =   0   'False
         End
         Begin MSMAPI.MAPISession MAPISession1 
            Left            =   6840
            Top             =   1440
            _ExtentX        =   1005
            _ExtentY        =   1005
            _Version        =   393216
            DownloadMail    =   -1  'True
            LogonUI         =   0   'False
            NewSession      =   0   'False
            Password        =   "S1c4F1n2020"
            UserName        =   "sicafin@masconmenos.com.mx"
         End
         Begin MSComCtl2.DTPicker DTPCalendario 
            Height          =   615
            Left            =   6360
            TabIndex        =   13
            Top             =   480
            Width           =   2535
            _ExtentX        =   4471
            _ExtentY        =   1085
            _Version        =   393216
            BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
               Name            =   "Verdana"
               Size            =   18
               Charset         =   0
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            Format          =   16842753
            CurrentDate     =   40576
         End
         Begin VB.Label lblMensaje2 
            Alignment       =   2  'Center
            Caption         =   "Procesando.... "
            BeginProperty Font 
               Name            =   "Verdana"
               Size            =   15.75
               Charset         =   0
               Weight          =   700
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000D&
            Height          =   375
            Left            =   480
            TabIndex        =   12
            Top             =   1200
            Width           =   8655
         End
         Begin VB.Label lblMensaje 
            Alignment       =   2  'Center
            Caption         =   "Proceso de Cierre de Día: "
            BeginProperty Font 
               Name            =   "Verdana"
               Size            =   20.25
               Charset         =   0
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            Height          =   495
            Left            =   720
            TabIndex        =   11
            Top             =   480
            Width           =   5415
         End
      End
      Begin VB.PictureBox Picture1 
         Height          =   30
         Left            =   3090
         ScaleHeight     =   30
         ScaleWidth      =   30
         TabIndex        =   5
         Top             =   270
         Width           =   30
      End
      Begin VB.CommandButton cmdCierre 
         Caption         =   "Ejecutar Cierre"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   300
         Left            =   7200
         TabIndex        =   4
         Top             =   2130
         Width           =   1485
      End
      Begin VB.CommandButton cmdCerrar 
         Caption         =   "&Cerrar"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   300
         Left            =   8850
         TabIndex        =   3
         Top             =   2130
         Width           =   885
      End
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   1
      Top             =   3450
      Width           =   9915
      _ExtentX        =   17489
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de Cierre de Día"
            TextSave        =   "Módulo de Cierre de Día"
         EndProperty
         BeginProperty Panel2 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   4410
            MinWidth        =   4410
         EndProperty
         BeginProperty Panel3 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Style           =   3
            Alignment       =   2
            Enabled         =   0   'False
            Object.Width           =   1058
            MinWidth        =   1058
            TextSave        =   "INS"
         EndProperty
         BeginProperty Panel4 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Style           =   1
            Alignment       =   2
            Enabled         =   0   'False
            Object.Width           =   1058
            MinWidth        =   1058
            TextSave        =   "CAPS"
         EndProperty
         BeginProperty Panel5 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Style           =   2
            Alignment       =   2
            Object.Width           =   1058
            MinWidth        =   1058
            TextSave        =   "NUM"
         EndProperty
      EndProperty
      BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
   End
End
Attribute VB_Name = "frmCierreDia"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private bCerrarForm As Boolean
Private dNoRegs As Long, dMonto As Double
Private sIdentificador As String
Private dFecha As Date

Private Sub cmdCerrar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    bCerrarForm = True
    Unload Me
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cmdCierre_Click()
Dim res As Variant
Dim sCadenaSQL As String
Dim acmd As New ADODB.Command
Dim oRstObtPago As New clsoAdoRecordset, oRstObtRes As New clsoAdoRecordset
Dim iDelete As Integer
Dim correo As Variant

lblMensaje2.Caption = ""

sCadenaSQL = ""
sCadenaSQL = sCadenaSQL & "SELECT COUNT(*) FROM TBL_CIERRE_DIA "
sCadenaSQL = sCadenaSQL & "WHERE FECHA_LIQUIDA IS NULL "
sCadenaSQL = sCadenaSQL & "AND FECHA_CALC = '" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "'"

If oRstObtPago.Estado = adStateOpen Then oRstObtPago.Cerrar

oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

Select Case oRstObtPago.HayRegistros
    Case 0  '-----   La consulta no retorno registros.   -----
        Screen.MousePointer = vbDefault
        MsgBox "No se puede ejecutar el Proceso de Cierre de Día, consulte con el administrador.", vbInformation + vbOKOnly, "Cierre de Día"
        Screen.MousePointer = vbHourglass
        oRstObtPago.Cerrar
    Case 1  '-----   Hay registros.                       -----
        If oRstObtPago.ObjSetRegistros.Fields(0).Value > 0 Then

           iDelete = 1

           res = MsgBox("El Proceso de Cierre de Día para la fecha seleccionada ya fué ejecutado.", vbInformation + vbOKOnly, "Cierre de Día")
           Exit Sub

        Else
            '-----   La consulta verifica que el cierre del dia previo se ha realizado ----
            sCadenaSQL = ""
            sCadenaSQL = sCadenaSQL & "SELECT COUNT(*) FROM TBL_CIERRE_DIA "
            sCadenaSQL = sCadenaSQL & "WHERE FECHA_LIQUIDA IS NULL "
            sCadenaSQL = sCadenaSQL & "AND FECHA_CALC = TO_DATE('" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "') - 1"

            If oRstObtPago.Estado = adStateOpen Then oRstObtPago.Cerrar

            oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

            Select Case oRstObtPago.HayRegistros
                Case 0  '-----   La consulta no retorno registros.   -----
                    Screen.MousePointer = vbDefault
                    MsgBox "No se puede ejecutar el Proceso de Cierre de Día, consulte con el administrador.", vbInformation + vbOKOnly, "Cierre de Día"
                    Screen.MousePointer = vbHourglass
                    oRstObtPago.Cerrar
                Case 1  '-----   Hay registros.
                    If oRstObtPago.ObjSetRegistros.Fields(0).Value > 0 Then
                        Screen.MousePointer = vbHourglass
                        sbBarraEstado.Panels(1).Text = "Procesando Cierre de Día..."
                        lblMensaje2.Caption = "Procesando...."
                        DTPCalendario.Enabled = False
                        cmdCierre.Enabled = False
                        cmdCerrar.Enabled = False
                        iDelete = 0

                        Set acmd = Nothing
                        With acmd
                            .CommandText = "sp_Cierre_Dia('" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "', 0)"
                            .CommandType = adCmdStoredProc
                            .ActiveConnection = oAccesoDatos.cnn.ObjConexion
                        End With
                        acmd.Execute

                        Set acmd = Nothing
                        With acmd
                            .CommandText = "PKG_CIERRE.SPGENDEVENGODIARIO('EMPFIN','" & Format(DTPCalendario.Value + 1, "yyyy/mm/dd") & "','" & sUsuarioApp & "')"
                            .CommandType = adCmdStoredProc
                            .ActiveConnection = oAccesoDatos.cnn.ObjConexion
                        End With
                        acmd.Execute
                        
                        'AMGM 30ABR2021 SE COMENTA ESTA PARTE DE MICROSEGUROS PORQUE NO APLICA PARA CULTIVA
                        'Ejecucion del proceso de Aplicacion de Pagos de Microseguro con Garantia Liquida
                        'Set acmd = Nothing
                        'With acmd
                        '    .CommandText = "SPAPLGLMICROSEG('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "')"
                        '    .CommandType = adCmdStoredProc
                        '    .ActiveConnection = oAccesoDatos.cnn.ObjConexion
                        'End With
                        'acmd.Execute

                        'Ejecucion del proceso de generacion de Alertas Relevantes PLD
                        Set acmd = Nothing
                        With acmd
                            .CommandText = "SPGENALERTARELPLD"
                            .CommandType = adCmdStoredProc
                            .ActiveConnection = oAccesoDatos.cnn.ObjConexion

                            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 6)  'Empresa
                            .Parameters.Append .CreateParameter(, adDate, adParamInput, 30)  'Fecha de Movimientos
                            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 6)  'Usuario
                            .Parameters.Append .CreateParameter(, adVarChar, adParamOutput, 200)  'Resultado de la ejecución del SP

                            .Parameters(0) = "EMPFIN"
                            .Parameters(1) = Format(DTPCalendario.Value, "yyyy/mm/dd")
                            .Parameters(2) = sUsuarioApp
                            .Parameters(3) = 0

                        End With
                        acmd.Execute

                        res = acmd.Parameters(3)

                        If Mid(acmd.Parameters(3), 1, 1) = "0" Then
                            MsgBox Mid(res, 3, Len(res) - 2), vbCritical + vbOKOnly, TITULO_MENSAJE
                        ElseIf Mid(acmd.Parameters(3), 1, 1) = "1" Then
                            'Ejecucion del proceso de generacion de Alertas Inusuales PLD
                            Set acmd = Nothing
                            With acmd
                                .CommandText = "SPGENALERTAINUPLD"
                                .CommandType = adCmdStoredProc
                                .ActiveConnection = oAccesoDatos.cnn.ObjConexion

                                .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 6)  'Empresa
                                .Parameters.Append .CreateParameter(, adDate, adParamInput, 30)  'Fecha de Movimientos
                                .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 6)  'Usuario
                                .Parameters.Append .CreateParameter(, adVarChar, adParamOutput, 200)  'Resultado de la ejecución del SP

                                .Parameters(0) = "EMPFIN"
                                .Parameters(1) = Format(DTPCalendario.Value, "yyyy/mm/dd")
                                .Parameters(2) = sUsuarioApp
                                .Parameters(3) = 0

                            End With
                            acmd.Execute

                            res = acmd.Parameters(3)

                            If Mid(acmd.Parameters(3), 1, 1) = "0" Then
                                MsgBox Mid(res, 3, Len(res) - 2), vbCritical + vbOKOnly, TITULO_MENSAJE
                            End If
                        End If
                    Else
                        res = MsgBox("No se puede ejecutar el Proceso de Cierre de Día, debido a que no se ha realizado el Cierre del Día Anterior.", vbInformation + vbOKOnly, "Cierre de Día")
                        Exit Sub
                        'Screen.MousePointer = vbDefault
                        'Screen.MousePointer = vbHourglass
                        'oRstObtPago.Cerrar
                    End If
            End Select
        End If
End Select


'>>>> AMGM 2015 CAMBIO PARA ENVIAR CORREO EN CASO DE QUE SE HAYAN GENERADO OPERACIONES RELEVANTE O INUSUALES EN LOS PROCESOS DE PLD QUE SE EJECUTAN EN EL CIERRE DE DIA

'>>> CORREO DEL OFICIAL DE CUMPLIMIENTO
sCadenaSQL = ""
sCadenaSQL = sCadenaSQL & "SELECT CORREO_OFICIAL FROM PARAMETROS_PLD "
sCadenaSQL = sCadenaSQL & "WHERE CDGEM = 'EMPFIN' "
sCadenaSQL = sCadenaSQL & "AND ESTATUS = 'A' "

If oRstObtPago.Estado = adStateOpen Then oRstObtPago.Cerrar

oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

Select Case oRstObtPago.HayRegistros
    Case 0  '-----   La consulta no retorno registros.   -----
        Screen.MousePointer = vbDefault
        MsgBox "No se puede realizar la consulta para obtener el correo del oficial de cumplimiento, consulte con el administrador.", vbInformation + vbOKOnly, "Cierre de Día"
        correo = oRstObtPago.ObjSetRegistros.Fields(0).Value
        Screen.MousePointer = vbHourglass
        oRstObtPago.Cerrar
    Case 1  '-----   Hay registros.                       -----
        correo = oRstObtPago.ObjSetRegistros.Fields(0).Value
End Select


'>>> RELEVANTES
sCadenaSQL = ""
sCadenaSQL = sCadenaSQL & "SELECT COUNT(*) FROM PLD_ALERTA "
sCadenaSQL = sCadenaSQL & "WHERE CDGEM = 'EMPFIN' "
sCadenaSQL = sCadenaSQL & "AND ALTA = '" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "' "
sCadenaSQL = sCadenaSQL & "AND CDGAL IN (SELECT CODIGO FROM CAT_ALERTA_PLD WHERE CDGTAPLD IN (SELECT CODIGO FROM CAT_TIPO_ALERTA_PLD WHERE CODIGO = 'R' )) "

If oRstObtPago.Estado = adStateOpen Then oRstObtPago.Cerrar

oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

Select Case oRstObtPago.HayRegistros
    Case 0  '-----   La consulta no retorno registros.   -----
        Screen.MousePointer = vbDefault
        MsgBox "No se puede realizar la consulta para alertas relevantes PLD, consulte con el administrador.", vbInformation + vbOKOnly, "Cierre de Día"
        Screen.MousePointer = vbHourglass
        oRstObtPago.Cerrar
    Case 1  '-----   Hay registros.                       -----
        res = oRstObtPago.ObjSetRegistros.Fields(0).Value
           
        Screen.MousePointer = vbHourglass
        sbBarraEstado.Panels(1).Text = "Procesando Cierre de Día..."
        lblMensaje2.Caption = "Procesando...."
        DTPCalendario.Enabled = False
        cmdCierre.Enabled = False
        cmdCerrar.Enabled = False

        MAPISession1.SignOn
        MAPIMessages1.SessionID = MAPISession1.SessionID
        MAPIMessages1.Compose
        MAPIMessages1.RecipDisplayName = correo
        MAPIMessages1.MsgSubject = " Alertas Operaciones Relevantes "
        MAPIMessages1.MsgNoteText = " Se ejecuto exitosamente el proceso de Alertas para Operaciones Relevantes generando un total de " & res & " alertas para la fecha " & Format(DTPCalendario.Value, "dd/mm/yyyy")
        MAPIMessages1.ResolveName
        MAPIMessages1.Send
        MAPISession1.SignOff

        'MsgBox "El Proceso de Cierre de Día para la fecha seleccionada ya fué ejecutado.", vbInformation + vbOKOnly, "Cierre de Día"
End Select

'>>> INUSUALES
sCadenaSQL = ""
sCadenaSQL = sCadenaSQL & "SELECT COUNT(*) FROM PLD_ALERTA "
sCadenaSQL = sCadenaSQL & "WHERE CDGEM = 'EMPFIN' "
sCadenaSQL = sCadenaSQL & "AND ALTA = '" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "' "
sCadenaSQL = sCadenaSQL & "AND CDGAL IN (SELECT CODIGO FROM CAT_ALERTA_PLD WHERE CDGTAPLD IN (SELECT CODIGO FROM CAT_TIPO_ALERTA_PLD WHERE CODIGO = 'I' )) "

If oRstObtPago.Estado = adStateOpen Then oRstObtPago.Cerrar

oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

Select Case oRstObtPago.HayRegistros
    Case 0  '-----   La consulta no retorno registros.   -----
        Screen.MousePointer = vbDefault
        MsgBox "No se puede realizar la consulta para alertas inusuales PLD, consulte con el administrador.", vbInformation + vbOKOnly, "Cierre de Día"
        Screen.MousePointer = vbHourglass
        oRstObtPago.Cerrar
    Case 1  '-----   Hay registros.                       -----
        res = oRstObtPago.ObjSetRegistros.Fields(0).Value
           
        Screen.MousePointer = vbHourglass
        sbBarraEstado.Panels(1).Text = "Procesando Cierre de Día..."
        lblMensaje2.Caption = "Procesando...."
        DTPCalendario.Enabled = False
        cmdCierre.Enabled = False
        cmdCerrar.Enabled = False

        MAPISession1.SignOn
        MAPIMessages1.SessionID = MAPISession1.SessionID
        MAPIMessages1.Compose
        MAPIMessages1.RecipDisplayName = correo
        MAPIMessages1.MsgSubject = " Alertas Operaciones Inusuales "
        MAPIMessages1.MsgNoteText = " Se ejecuto exitosamente el proceso de Alertas para Operaciones Inusuales generando un total de " & res & " alertas para la fecha " & Format(DTPCalendario.Value, "dd/mm/yyyy")
        MAPIMessages1.ResolveName
        MAPIMessages1.Send
        MAPISession1.SignOff

        'MsgBox "El Proceso de Cierre de Día para la fecha seleccionada ya fué ejecutado.", vbInformation + vbOKOnly, "Cierre de Día"
End Select

'<<<<< AMGM 2015
If iDelete = 1 Then

    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "SELECT COUNT(*) FROM TBL_CIERRE_DIA "
    sCadenaSQL = sCadenaSQL & "WHERE FECHA_LIQUIDA IS NULL "
    sCadenaSQL = sCadenaSQL & "AND FECHA_CALC = '" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "' "
    sCadenaSQL = sCadenaSQL & "AND (PROCESADO = 0 OR PROCESADO IS NULL) "
    
    If oRstObtPago.Estado = adStateOpen Then oRstObtPago.Cerrar
    
    oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstObtPago.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            MsgBox "No se puede ejecutar el Proceso de Cierre de Día, consulte con el administrador.", vbInformation + vbOKOnly, "Cierre de Día"
            Screen.MousePointer = vbHourglass
            oRstObtPago.Cerrar
        Case 1  '-----   Hay registros.                       -----
            If oRstObtPago.ObjSetRegistros.Fields(0).Value > 0 Then
               
                res = MsgBox("El Proceso de Cierre de Día para la fecha seleccionada fué ejecutado de manera incompleta," & vbNewLine & _
                             "¿Deseas procesar solo los registros pendientes?, caso contrario reprocesara la informaciòn por completo.", vbQuestion + vbYesNo, "Cierre de Día")
                
                Screen.MousePointer = vbHourglass
                sbBarraEstado.Panels(1).Text = "Procesando Cierre de Día..."
                lblMensaje2.Caption = "Procesando...."
                DTPCalendario.Enabled = False
                cmdCierre.Enabled = False
                cmdCerrar.Enabled = False
                iDelete = 0
            
                If (res = vbYes) Then
        
                    Set acmd = Nothing
                    With acmd
                        .CommandText = "sp_Cierre_Dia('" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "', 1)"
                        .CommandType = adCmdStoredProc
                        .ActiveConnection = oAccesoDatos.cnn.ObjConexion
                    End With
                    acmd.Execute
            
                Else

                    Set acmd = Nothing
                    With acmd
                        .CommandText = "sp_Cierre_Dia('" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "', 0)"
                        .CommandType = adCmdStoredProc
                        .ActiveConnection = oAccesoDatos.cnn.ObjConexion
                    End With
                    acmd.Execute

                End If
            
            Else
            
               res = MsgBox("El Proceso de Cierre de Día para la fecha seleccionada ya fué ejecutado.", vbInformation + vbOKOnly, "Cierre de Día")
               Exit Sub
            
            End If
    End Select

End If

Screen.MousePointer = vbDefault
MsgBox "El Cierre de Día " & DTPCalendario.Value & " ha sido Procesado.", vbInformation + vbOKOnly, "Cierre de Día"
DTPCalendario.Enabled = True

lblMensaje2.Caption = "Proceso de Cierre de Día Terminado!"
sbBarraEstado.Panels(1).Text = "Módulo de Cierre de Día"
cmdCierre.Enabled = True
cmdCerrar.Enabled = True

End Sub

Private Sub DTPCalendario_CloseUp()
'    If DTPCalendario.Value > dFecha Then
'        MsgBox "La fecha seleccionada para el Proceso de Cierre no puede ser igual o mayor a la fecha actual.", vbExclamation + vbOKOnly, "Cierre de Día"
'        DTPCalendario.Value = dFecha
'    End If
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    bCerrarForm = False
    sbBarraEstado.Panels(1).Text = "Módulo de Cierre de Día"
    cmdCierre.Visible = True
    lblMensaje.Caption = lblMensaje.Caption
    lblMensaje2.Caption = ""
    dFecha = FECHA_CALC
    DTPCalendario.Value = dFecha
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Unload(Cancel As Integer)
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If (bCerrarForm = False) Then Cancel = 1
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Function FECHA_CALC() As Date
    Dim oRstPago As New clsoAdoRecordset

    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
              
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "SELECT TRUNC(SYSDATE) FROM DUAL"
    
    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
    oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstPago.HayRegistros
        Case 1   '-----   La consulta SI retorno registros.   -----
           
            FECHA_CALC = oRstPago.ObjSetRegistros.Fields(0)
            oRstPago.Cerrar
            
    End Select
End Function
