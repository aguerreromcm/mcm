VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmRegPagos 
   AutoRedraw      =   -1  'True
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Importación de Pagos"
   ClientHeight    =   7065
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   7665
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   7065
   ScaleWidth      =   7665
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox pbContImportacion 
      Align           =   1  'Align Top
      BackColor       =   &H00FFF9F9&
      Height          =   6045
      Left            =   0
      ScaleHeight     =   5985
      ScaleWidth      =   7605
      TabIndex        =   7
      Top             =   735
      Width           =   7665
      Begin VB.TextBox txtCantidad 
         BeginProperty DataFormat 
            Type            =   0
            Format          =   "0.00"
            HaveTrueFalseNull=   0
            FirstDayOfWeek  =   0
            FirstWeekOfYear =   0
            LCID            =   2058
            SubFormatType   =   0
         EndProperty
         Height          =   285
         Left            =   240
         MaxLength       =   9
         TabIndex        =   26
         Top             =   3840
         Width           =   1575
      End
      Begin VB.ComboBox cmbCiclo 
         Height          =   315
         Left            =   240
         TabIndex        =   24
         Top             =   2400
         Width           =   735
      End
      Begin VB.TextBox txtNomGrupo 
         BackColor       =   &H8000000F&
         Enabled         =   0   'False
         Height          =   285
         Left            =   1440
         TabIndex        =   23
         Top             =   1800
         Width           =   5895
      End
      Begin VB.TextBox txtGrupo 
         Height          =   285
         Left            =   240
         MaxLength       =   6
         TabIndex        =   22
         Top             =   1800
         Width           =   975
      End
      Begin VB.OptionButton optPagoGL 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Garantía Líquida"
         BeginProperty Font 
            Name            =   "Courier New"
            Size            =   8.25
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   375
         Left            =   2520
         TabIndex        =   20
         Top             =   1080
         Width           =   2415
      End
      Begin VB.OptionButton optPagoMC 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Pago"
         BeginProperty Font 
            Name            =   "Courier New"
            Size            =   8.25
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   375
         Left            =   240
         TabIndex        =   19
         Top             =   1080
         Width           =   1815
      End
      Begin VB.CommandButton cmdImportacion 
         Caption         =   "&Importar pagos..."
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
         Left            =   2760
         TabIndex        =   11
         Top             =   5640
         Width           =   1600
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
         Left            =   6360
         TabIndex        =   10
         Top             =   5640
         Width           =   1000
      End
      Begin VB.ComboBox cbEmpresa 
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
         ItemData        =   "frmRegPagos.frx":0000
         Left            =   210
         List            =   "frmRegPagos.frx":0007
         Style           =   2  'Dropdown List
         TabIndex        =   9
         Top             =   330
         Width           =   1395
      End
      Begin VB.ComboBox cbCuentaBancaria 
         BeginProperty Font 
            Name            =   "Courier New"
            Size            =   8.25
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   330
         Left            =   240
         Style           =   2  'Dropdown List
         TabIndex        =   8
         Top             =   4440
         Width           =   7185
      End
      Begin MSComCtl2.DTPicker DPFechaPago 
         Height          =   300
         Left            =   240
         TabIndex        =   12
         Top             =   3150
         Width           =   1605
         _ExtentX        =   2831
         _ExtentY        =   529
         _Version        =   393216
         BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         CalendarTitleBackColor=   12582912
         CalendarTitleForeColor=   16777215
         Format          =   16777217
         CurrentDate     =   38597
      End
      Begin VB.Label lblMensaje 
         Alignment       =   2  'Center
         BackColor       =   &H00FFF9F9&
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   27.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   1095
         Left            =   2640
         TabIndex        =   29
         Top             =   2640
         Width           =   4095
      End
      Begin VB.Label Label11 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Nombre del Grupo:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   1440
         TabIndex        =   28
         Top             =   1560
         Width           =   1575
      End
      Begin VB.Label Label9 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Cantidad:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   27
         Top             =   3600
         Width           =   1095
      End
      Begin VB.Label Label7 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Ciclo:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   25
         Top             =   2160
         Width           =   975
      End
      Begin VB.Label Label5 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Grupo:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   21
         Top             =   1560
         Width           =   735
      End
      Begin VB.Label lbArchivo 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Tipo de Depósito:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   180
         Left            =   240
         TabIndex        =   18
         Top             =   840
         Width           =   1335
      End
      Begin VB.Label Label1 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Empresa:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   180
         Left            =   210
         TabIndex        =   17
         Top             =   120
         Width           =   720
      End
      Begin VB.Label Label2 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Cuenta &Bancaria:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   180
         Left            =   240
         TabIndex        =   16
         Top             =   4200
         Width           =   1305
      End
      Begin VB.Label Label3 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Descripción:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   180
         Left            =   240
         TabIndex        =   15
         Top             =   4920
         Width           =   960
      End
      Begin VB.Label lbBanco 
         BorderStyle     =   1  'Fixed Single
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   285
         Left            =   240
         TabIndex        =   14
         Top             =   5160
         Width           =   7185
      End
      Begin VB.Label Label12 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Fecha de Pago:"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   180
         Left            =   240
         TabIndex        =   13
         Top             =   2910
         Width           =   1155
      End
   End
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   7665
      TabIndex        =   3
      Top             =   0
      Width           =   7665
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   360
         Picture         =   "frmRegPagos.frx":0013
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   30
         Top             =   0
         Width           =   1095
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
         Left            =   7440
         TabIndex        =   6
         Top             =   360
         Width           =   135
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
         Left            =   6240
         TabIndex        =   5
         Top             =   480
         Width           =   1170
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Registro de Depósitos"
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
         Left            =   1920
         TabIndex        =   4
         Top             =   60
         Width           =   4770
      End
   End
   Begin VB.PictureBox pbNoIdentificado 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   195
      Left            =   1320
      ScaleHeight     =   195
      ScaleWidth      =   180
      TabIndex        =   2
      Top             =   7110
      Visible         =   0   'False
      Width           =   180
   End
   Begin VB.PictureBox pbIdentificado 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   180
      Left            =   1080
      ScaleHeight     =   180
      ScaleWidth      =   180
      TabIndex        =   1
      Top             =   7110
      Visible         =   0   'False
      Width           =   180
   End
   Begin MSComDlg.CommonDialog cdlgImportacion 
      Left            =   4680
      Top             =   6480
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   0
      Top             =   6780
      Width           =   7665
      _ExtentX        =   13520
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   7056
            MinWidth        =   7056
            Text            =   "Módulo de Registro de Depósitos"
            TextSave        =   "Módulo de Registro de Depósitos"
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
Attribute VB_Name = "frmRegPagos"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private iStatus As Integer
Private sMensaje As String, sMensajePago As String, sCadenaSQL As String
Private dMontoTab1 As Double, dMontoTab2 As Double, dMontoTab3 As Double, dMontoTab4 As Double, dMontoTab5 As Double
Private sDocImpActual As String, sDocImpAnterior As String
Private sSecuenciaIM As String, dImporteIM As Double, lRegsIM As Long, sEmpresa As String, sCtaBancaria As String, sSecuenciaMP As String, sSecuenciaPDI As String, sSecuenciaMB As String
Private sCodigoIM As String, sTipoCliente As String, sCicloIM As String, lNoPagoMP As Long, lNoPagoPDI As Long
Private lNoRegsExcel As Long, lContador As Long
Private bCerrarForm As Boolean
Private bImportarPago As Boolean
Private sIdentificador As String
Private sReferencia As String
Private bCargaCiclos As Boolean

Private Const NUM_COLS_PROCESADOS = 9
Private Const NUM_COLS_IDENTIFICADOS = 10
Private Const NUM_COLS_NOIDENTIFICADOS = 10
Private Const NUM_COLS_NOIMPORTADOS = 10
Private Const NUM_COLS_ARQUEOCAJA = 10
Private Const NOMBRE_FONT = "Verdana"
Private Const TAMAÑO_FONT = 8

Private Sub cmdCerrar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    bCargaCiclos = False
    bCerrarForm = True
    Unload Me
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cmdImportacion_Click()
    Dim res As Variant
    Dim sMensaje As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    If cbEmpresa.Text = "" Or txtGrupo.Text = "" Or cmbCiclo.Text = "" Or txtCantidad.Text = "" Or cbCuentaBancaria.Text = "" And dpFechaPago.Value = "" Or lblMensaje = "" Then
        sMensaje = ""
        sMensaje = sMensaje & "Favor de verificar que alguno de los siguientes campos no este vacio: " & vbNewLine
        sMensaje = sMensaje & vbTab & "1) Empresa." & vbNewLine
        sMensaje = sMensaje & vbTab & "2) Tipo de Pago." & vbNewLine
        sMensaje = sMensaje & vbTab & "3) Codigo de Grupo." & vbNewLine
        sMensaje = sMensaje & vbTab & "4) Ciclo." & vbNewLine
        sMensaje = sMensaje & vbTab & "5) Fecha de Pago." & vbNewLine
        sMensaje = sMensaje & vbTab & "6) Cantidad" & vbNewLine
        sMensaje = sMensaje & vbTab & "7) Cuenta Bancaria" & vbNewLine
        MsgBox sMensaje, vbCritical + vbOKOnly, TITULO_MENSAJE
        
        Screen.MousePointer = vbDefault
        Exit Sub
    End If

    If optPagoMC.Value And dpFechaPago.Value < CDate(Mid(cmbCiclo.Text, (InStr(cmbCiclo.Text, "/") - 2), 10)) Then
        MsgBox "La fecha de aplicación del pago(" & dpFechaPago.Value & ") no puede ser menor a la fecha de inicio(" & Mid(cmbCiclo.Text, (InStr(cmbCiclo.Text, "/") - 2), 10) & ") del crédito. Favor de verificar", vbCritical + vbOKOnly, TITULO_MENSAJE
        Screen.MousePointer = vbDefault
        Exit Sub
    End If
        
        sFechaCarga = Format(Date, "YYYY/MM/DD")
        sMensaje = ""
        sMensaje = sMensaje & "¿Esta seguro(a) que desea importar el depósito para?" & vbNewLine & vbNewLine
        sMensaje = sMensaje & "Empresa:" & vbTab & vbTab & cbEmpresa.Text & vbNewLine
        sMensaje = sMensaje & "Tipo Dep:" & vbTab & vbTab & lblMensaje & vbNewLine
        sMensaje = sMensaje & "Grupo:" & vbTab & vbTab & txtGrupo.Text & vbNewLine
        sMensaje = sMensaje & "Nombre Grupo:" & vbTab & txtNomGrupo.Text & vbNewLine
        sMensaje = sMensaje & "Ciclo:" & vbTab & vbTab & Mid(cmbCiclo.Text, 1, 2) & vbNewLine
        sMensaje = sMensaje & "Fecha:" & vbTab & vbTab & dpFechaPago.Value & vbNewLine
        sMensaje = sMensaje & "Cantidad:" & vbTab & vbTab & txtCantidad.Text & vbNewLine
        sMensaje = sMensaje & "Cta. bancaria:" & vbTab & cbCuentaBancaria.Text & vbNewLine
        sMensaje = sMensaje & "Descripción:" & vbTab & Trim(lbBanco.Caption)
        Screen.MousePointer = vbDefault
        res = MsgBox(sMensaje, vbQuestion + vbYesNo, TITULO_MENSAJE)
        Screen.MousePointer = vbHourglass
        If (res = vbYes) Then
            Call EjecutarImportacion
            'cmdImportacion.Enabled = False
        End If

            cbEmpresa.SetFocus
            cmdImportacion.Visible = True
            'cmdImportacion.Enabled = False
            dpFechaPago.Value = Date
            cbEmpresa.ListIndex = 0
            cbCuentaBancaria.ListIndex = 0
            cmbCiclo.Enabled = False
            'DPFechaPago.Enabled = False
            txtCantidad.Enabled = False
            cbCuentaBancaria.Enabled = False
            cmbCiclo.Clear
            bCargaCiclos = False
            txtCantidad.Text = ""
            txtGrupo.Text = ""
            txtNomGrupo.Text = ""
            
            If bIdentifica Then
                Call cmdCerrar_Click
            End If

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
Dim l As Integer, i As Integer
Dim oRstPago As New clsoAdoRecordset
Dim sCadenaSQL As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    'Public sEmpresa As String, fdPago As Date, sCantidad As String, sCtaBco As String, bIdentifica As Boolean
    
    '--- CAMBIO POR DIEGO TRINADO QUE PERMITE OBTENER LA FECHA DE LA BD
    sCadenaSQL = ""
    sCadenaSQL = "select NVL(FNFECHAFINHABIL(TRUNC(SYSDATE)),TRUNC(SYSDATE)) fecha "
    sCadenaSQL = sCadenaSQL & " from dual "
    
    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        
    Select Case oRstPago.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la información solicitada." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstPago.Cerrar
            Screen.MousePointer = vbDefault
            Exit Sub
        Case 1  '-----   Hay registros.                       -----
            dpFechaPago.Value = oRstPago.ObjSetRegistros.Fields("fecha").Value
            'DPFechaPago.MinDate = oRstPago.ObjSetRegistros.Fields("fecha").Value
            'DPFechaPago.MaxDate = oRstPago.ObjSetRegistros.Fields("fecha").Value
            oRstPago.Cerrar
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstPago.Cerrar
            Screen.MousePointer = vbDefault
            Exit Sub
    End Select
                
    bCerrarForm = False
    cmdImportacion.Visible = True
    cmdImportacion.Enabled = True
    sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
    cbEmpresa.ListIndex = 0
    cbCuentaBancaria.ListIndex = 0
    txtGrupo.Enabled = False
    cmbCiclo.Enabled = False
    'DPFechaPago.Enabled = False   '--- CAMBIO 07/10/2010 --- JOSE ANTONIO RENDON TORRES --- VALIDAR CONTRA FECHA SERVER --- INICIO ---'
    txtCantidad.Enabled = False
    cbCuentaBancaria.Enabled = True

    If bIdentifica Then
        'DPFechaPago.Value = fdPago
        txtCantidad.Text = sCantidad

        l = cbCuentaBancaria.ListCount
        For i = 0 To l - 1
            If Mid(cbCuentaBancaria.List(i), 1, 2) = sCtaBco Then cbCuentaBancaria.ListIndex = i
        Next
    
    End If
    
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

Private Sub cbCuentaBancaria_Click()
    Dim sConsultaSQL As String
    Dim oCtaBanRst As New clsoAdoRecordset
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    lbBanco.Caption = ""
    'Call BorrarFilasGrids
    'cmdExpExcel.Visible = False
    
    If (oAccesoDatos.EstadoConexion <> adStateOpen) Then
        Screen.MousePointer = vbDefault
        MsgBox "La aplicación se desconectó de la Base de Datos y se presentó un error al intentar iniciar la sesión nuevamente." & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
        Screen.MousePointer = vbHourglass
        oAccesoDatos.rst.Cerrar
        oAccesoDatos.cnn.Cerrar
        bCerrarApp = True
        Unload frmInicioSesion
        Unload frmPrincipal
        Unload Me
    Else
        'oAccesoDatos.rst.Cerrar
        
        sConsultaSQL = ""
        sConsultaSQL = sConsultaSQL & "select   b.*" & vbNewLine
        sConsultaSQL = sConsultaSQL & "from     cb a, ib b" & vbNewLine
        sConsultaSQL = sConsultaSQL & "where    a.codigo = '" & Trim(Mid(cbCuentaBancaria.Text, 1, 4)) & "'" & vbNewLine
        sConsultaSQL = sConsultaSQL & "and      b.codigo = a.cdgib" & vbNewLine
        sConsultaSQL = sConsultaSQL & "and      a.cdgem = b.cdgem" & vbNewLine
        sConsultaSQL = sConsultaSQL & "and      a.cdgem = '" & cbEmpresa.Text & "'" & vbNewLine
        
        oCtaBanRst.Abrir sConsultaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        
        Select Case oCtaBanRst.HayRegistros
            Case 0  '-----   La consulta no retorno registros.   -----
                MsgBox "No instituciones bancarias disponibles para la empresa " & cbEmpresa.Text & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oCtaBanRst.Cerrar
                oAccesoDatos.rst.Cerrar
                oAccesoDatos.cnn.Cerrar
                bCerrarApp = True
                Screen.MousePointer = vbDefault
                Unload frmInicioSesion
                Unload frmPrincipal
                Unload Me
            Case 1  '-----   Hay registros.                       -----
                lbBanco.Caption = " " & oCtaBanRst.ObjSetRegistros.Fields("NOMBRE").Value
                oCtaBanRst.Cerrar
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                Screen.MousePointer = vbDefault
                MsgBox "La aplicación no pudo obtener la lista de instituciones bancarias para la empresa " & cbEmpresa.Text & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                Screen.MousePointer = vbHourglass
                oCtaBanRst.Cerrar
                oAccesoDatos.rst.Cerrar
                oAccesoDatos.cnn.Cerrar
                bCerrarApp = True
                Screen.MousePointer = vbDefault
                Unload frmInicioSesion
                Unload frmPrincipal
                Unload Me
        End Select
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cbEmpresa_Click()
    Dim sConsultaSQL As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    

    cbCuentaBancaria.Clear

    
    If (oAccesoDatos.EstadoConexion <> adStateOpen) Then
        oAccesoDatos.rst.Cerrar
        oAccesoDatos.cnn.Cerrar
        
        oAccesoDatos.Conectar (sAmbiente)
        
        If (oAccesoDatos.EstadoConexion = adStateOpen) Then
            oAccesoDatos.rst.Abrir "select * from PE where CODIGO = '" & sUsuarioApp & "' and CLAVE = CODIFICA('" & sPasswordApp & "')", oAccesoDatos.cnn.ObjConexion, adOpenDynamic, adLockOptimistic
            
            Select Case oAccesoDatos.rst.HayRegistros
                Case 0  '-----   La consulta no retorno registros.   -----
                    MsgBox "La aplicación se desconectó de la Base de Datos y no fue posible realizar la conexión con el usuario: " & sUsuarioApp & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oAccesoDatos.rst.Cerrar
                    oAccesoDatos.cnn.Cerrar
                    bCerrarApp = True
                    Unload frmInicioSesion
                    Unload frmPrincipal
                    Unload Me
                Case 1  '-----   Hay registros.                       -----
                    oAccesoDatos.rst.Cerrar
                    
                    sConsultaSQL = ""
                    sConsultaSQL = sConsultaSQL & "select   a.codigo," & vbNewLine
                    sConsultaSQL = sConsultaSQL & "         a.numero," & vbNewLine
                    sConsultaSQL = sConsultaSQL & "         a.nombre as nom_cuenta," & vbNewLine
                    sConsultaSQL = sConsultaSQL & "         b.codigo as cod_banco," & vbNewLine
                    sConsultaSQL = sConsultaSQL & "         b.nombre as nom_banco" & vbNewLine
                    sConsultaSQL = sConsultaSQL & "from     cb a, ib b" & vbNewLine
                    sConsultaSQL = sConsultaSQL & "where    a.cdgem = '" & cbEmpresa.Text & "'" & vbNewLine
                    sConsultaSQL = sConsultaSQL & "and      a.cdgib = b.codigo" & vbNewLine
                    sConsultaSQL = sConsultaSQL & "and      a.cdgem = b.cdgem" & vbNewLine
                    'AMGM 13JUN2011 CAMBIO PARA HACER QUE TODOS LOS PAGOS ELIMINADOS PASEN POR LA CUBETA DE NO IDENTIFICADOS
                    If bIdentifica = False Then  'CON ESTO NOS ASEGURAMOS QUE SOLO SE REGISTREN PAGOS MANUALES CUANDO SON TRASPASOS DE GL A CARTERA
                        If ValidaUsuarioPagos(sUsuarioApp) = 0 Then  'CON ESTO TOMAMOS EN CUENTA LAS EXCEPCIONES PARA LOS USUARIOS AUTORIZADOS
                            sConsultaSQL = sConsultaSQL & "and      a.codigo IN ('12','98') " & vbNewLine
                        End If
                    End If
                    sConsultaSQL = sConsultaSQL & "order by a.codigo asc"
                    
                    oAccesoDatos.rst.Abrir sConsultaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
                    
                    Select Case oAccesoDatos.rst.HayRegistros
                        Case 0  '-----   La consulta no retorno registros.   -----
                            MsgBox "No hay cuentas bancarias disponibles para la empresa " & cbEmpresa.Text & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                            oAccesoDatos.rst.Cerrar
                            oAccesoDatos.cnn.Cerrar
                            bCerrarApp = True
                            Unload frmInicioSesion
                            Unload frmPrincipal
                            Unload Me
                        Case 1  '-----   Hay registros.                       -----
                            lbBanco.Caption = " " & oAccesoDatos.rst.ObjSetRegistros.Fields("NOM_BANCO").Value
                           
                            While (Not oAccesoDatos.rst.FinDeArchivo)
                                cbCuentaBancaria.AddItem oAccesoDatos.rst.ObjSetRegistros.Fields("CODIGO").Value & "  " & ConcatenarCaracter(oAccesoDatos.rst.ObjSetRegistros.Fields("NUMERO").Value, adDerecha, 13, " ") & "  " & oAccesoDatos.rst.ObjSetRegistros.Fields("NOM_CUENTA").Value
                                oAccesoDatos.rst.IrAlRegSiguiente
                            Wend
                            cbCuentaBancaria.ListIndex = 0
                            oAccesoDatos.rst.Cerrar
                        Case 2  '-----   El Query no se pudo ejecutar.        -----
                            Screen.MousePointer = vbDefault
                            MsgBox "La aplicación no pudo obtener la lista de cuentas bancarias para la empresa " & cbEmpresa.Text & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                            Screen.MousePointer = vbHourglass
                            oAccesoDatos.rst.Cerrar
                            oAccesoDatos.cnn.Cerrar
                            bCerrarApp = True
                            Unload frmInicioSesion
                            Unload frmPrincipal
                            Unload Me
                    End Select
                Case 2  '-----   El Query no se pudo ejecutar.        -----
                    Screen.MousePointer = vbDefault
                    MsgBox "La aplicación se desconectó de la Base de Datos y se presentó un error al intentar iniciar la sesión nuevamente." & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    Screen.MousePointer = vbHourglass
                    oAccesoDatos.rst.Cerrar
                    oAccesoDatos.cnn.Cerrar
                    bCerrarApp = True
                    Screen.MousePointer = vbDefault
                    Unload frmInicioSesion
                    Unload frmPrincipal
                    Unload Me
            End Select
        Else
            Screen.MousePointer = vbDefault
            MsgBox "No fue posible abrir la Conexion con la Base de Datos." & vbNewLine & vbNewLine & "Intentelo nuevamente o consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
        End If
    Else
        oAccesoDatos.rst.Cerrar
        
        sConsultaSQL = ""
        sConsultaSQL = sConsultaSQL & "select   a.codigo," & vbNewLine
        sConsultaSQL = sConsultaSQL & "         a.numero," & vbNewLine
        sConsultaSQL = sConsultaSQL & "         a.nombre as nom_cuenta," & vbNewLine
        sConsultaSQL = sConsultaSQL & "         b.codigo as cod_banco," & vbNewLine
        sConsultaSQL = sConsultaSQL & "         b.nombre as nom_banco" & vbNewLine
        sConsultaSQL = sConsultaSQL & "from     cb a, ib b" & vbNewLine
        sConsultaSQL = sConsultaSQL & "where    a.cdgem = '" & cbEmpresa.Text & "'" & vbNewLine
        sConsultaSQL = sConsultaSQL & "and      a.cdgib = b.codigo" & vbNewLine
        sConsultaSQL = sConsultaSQL & "and      a.cdgem = b.cdgem" & vbNewLine
        'AMGM 13JUN2011 CAMBIO PARA HACER QUE TODOS LOS PAGOS ELIMINADOS PASEN POR LA CUBETA DE NO IDENTIFICADOS
        If bIdentifica = False Then  'CON ESTO NOS ASEGURAMOS QUE SOLO SE REGISTREN PAGOS MANUALES CUANDO SON TRASPASOS DE GL A CARTERA
            If ValidaUsuarioPagos(sUsuarioApp) = 0 Then  'CON ESTO TOMAMOS EN CUENTA LAS EXCEPCIONES PARA LOS USUARIOS AUTORIZADOS
                sConsultaSQL = sConsultaSQL & "and      a.codigo IN ('12','98') " & vbNewLine
            End If
        End If
        sConsultaSQL = sConsultaSQL & "order by a.codigo asc"
        
        oAccesoDatos.rst.Abrir sConsultaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        
        Select Case oAccesoDatos.rst.HayRegistros
            Case 0  '-----   La consulta no retorno registros.   -----
                MsgBox "No hay cuentas bancarias disponibles para la empresa " & cbEmpresa.Text & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oAccesoDatos.rst.Cerrar
                oAccesoDatos.cnn.Cerrar
                bCerrarApp = True
                Screen.MousePointer = vbDefault
                Unload frmInicioSesion
                Unload frmPrincipal
                Unload Me
            Case 1  '-----   Hay registros.                       -----
                lbBanco.Caption = " " & oAccesoDatos.rst.ObjSetRegistros.Fields("NOM_BANCO").Value
                            
                While (Not oAccesoDatos.rst.FinDeArchivo)
                    cbCuentaBancaria.AddItem oAccesoDatos.rst.ObjSetRegistros.Fields("CODIGO").Value & "  " & ConcatenarCaracter(oAccesoDatos.rst.ObjSetRegistros.Fields("NUMERO").Value, adDerecha, 13, " ") & "  " & oAccesoDatos.rst.ObjSetRegistros.Fields("NOM_CUENTA").Value
                    oAccesoDatos.rst.IrAlRegSiguiente
                Wend
                cbCuentaBancaria.ListIndex = 0
                oAccesoDatos.rst.Cerrar
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                Screen.MousePointer = vbDefault
                MsgBox "La aplicación no pudo obtener la lista de cuentas bancarias para la empresa " & cbEmpresa.Text & vbNewLine & vbNewLine & "Será necesario iniciar una nueva sesión." & vbNewLine & vbNewLine & "Si continua teniendo problemas consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                Screen.MousePointer = vbHourglass
                oAccesoDatos.rst.Cerrar
                oAccesoDatos.cnn.Cerrar
                bCerrarApp = True
                Screen.MousePointer = vbDefault
                Unload frmInicioSesion
                Unload frmPrincipal
                Unload Me
        End Select
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Function EjecutarImportacion() As Boolean
    Dim lCont As Long, sCadena As String, oRstPago As New clsoAdoRecordset, respuesta As Variant, lContador As Long
    Dim lNoPagosImp As Long, lIndice As Long
    Dim intParcialidad As Double
    Dim intDiferencia As Double
    Dim booDifer As Boolean
    Dim acmd As New ADODB.Command  'AMGM 25JUL2007   Este comando se utiliza para la ejecución del SP
    Dim SecPDI As String
    Dim dFecha As Date
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    EjecutarImportacion = False

    lContador = 0

    sIdentificador = Format(Date, "DDMMYYYY") & Format(Time, "HHNNSS")
    sReferencia = IIf(optPagoMC.Value, "P", "0") & txtGrupo.Text & Mid(cmbCiclo.Text, Len(cmbCiclo.Text) - 1, 2)

    oAccesoDatos.cnn.IniciarTrans
    
        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                  
'---RETA-HABILITAR CAMBIO PARA VERSION ADMINCREDV23 DEL 09022011-------'
'---DESCOMENTARISAR EL SIGUIENTE CODIGO---'

'''        '--- CAMBIO 07/10/2010 --- JOSE ANTONIO RENDON TORRES --- VALIDAR CONTRA FECHA SERVER --- INICIO ---'
'''        sCadenaSQL = ""
'''        sCadenaSQL = sCadenaSQL & "SELECT TRUNC(SYSDATE) FROM DUAL"
'''
'''        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
'''        oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
'''
'''        Select Case oRstPago.HayRegistros
'''            Case 1   '-----   La consulta SI retorno registros.   -----
'''                Screen.MousePointer = vbDefault
'''
'''                dFecha = oRstPago.ObjSetRegistros.Fields(0)
'''
'''                If DPFechaPago.Value <> dFecha Then
'''                    MsgBox "La fecha de tu equipo no es igual a la del servidor," & vbNewLine & "favor de verificar y corregir." & vbNewLine & vbNewLine & "Fecha Equipo: " & DPFechaPago.Value & vbNewLine & "Fecha Servidor: " & dFecha, vbInformation + vbOKOnly, TITULO_MENSAJE
'''                    Screen.MousePointer = vbHourglass
'''                    oRstPago.Cerrar
'''                    Screen.MousePointer = vbDefault
'''                    oAccesoDatos.cnn.AceptarTrans
'''                    Exit Function
'''                Else
'''                    DPFechaPago.Value = Format(dFecha, "YYYY/MM/DD")
'''                    Screen.MousePointer = vbHourglass
'''                    oRstPago.Cerrar
'''                    Screen.MousePointer = vbDefault
'''                    oAccesoDatos.cnn.AceptarTrans
'''                End If
'''        End Select
'''        '--- CAMBIO 07/10/2010 --- JOSE ANTONIO RENDON TORRES --- VALIDAR CONTRA FECHA SERVER --- FIN ---'
        
        If bIdentifica Then
        
                sCadenaSQL = ""
                sCadenaSQL = "select estatus, cdgpe_iden, fecha_iden "
                sCadenaSQL = sCadenaSQL & "  from pdi "
                sCadenaSQL = sCadenaSQL & " where cdgem = '" & cbEmpresa.Text & "'"
                sCadenaSQL = sCadenaSQL & "   and fdeposito = '" & Format(fdPago, "YYYY/MM/DD") & "'"
                sCadenaSQL = sCadenaSQL & "   and secuencia = '" & sSecuencia & "'"
        
                If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        
                Select Case oRstPago.HayRegistros
                    Case 0  '-----   La consulta no retorno registros.   -----
                        Screen.MousePointer = vbDefault
                        MsgBox "La aplicación no encontro información referencte al pago..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                        Screen.MousePointer = vbHourglass
                        oRstPago.Cerrar
                        Screen.MousePointer = vbDefault
                        oAccesoDatos.cnn.AceptarTrans
                        Exit Function
                    Case 1  '-----   Hay registros.                       -----
                        '-----   Llenamos el grid con la información encontrada   -----
                        If oRstPago.ObjSetRegistros.Fields(0) <> "RE" Then
                            Screen.MousePointer = vbDefault
                            MsgBox "El pago ya fue identificado por el usuario: " & oRstPago.ObjSetRegistros.Fields(1) & " en la fecha: " & oRstPago.ObjSetRegistros.Fields(2) & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                            Screen.MousePointer = vbHourglass
                            oRstPago.Cerrar
                            Screen.MousePointer = vbDefault
                            oAccesoDatos.cnn.AceptarTrans
                            bResIdenPago = True
                            Exit Function
                        End If
                    Case 2  '-----   El Query no se pudo ejecutar.        -----
                        Screen.MousePointer = vbDefault
                        MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                        Screen.MousePointer = vbHourglass
                        oRstPago.Cerrar
                        Screen.MousePointer = vbDefault
                        oAccesoDatos.cnn.AceptarTrans
                        Exit Function
                End Select
        End If
        
        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar

        Set acmd = Nothing
        With acmd
            '.CommandText = "spImportaPagoSOF"
            .CommandText = "PKG_ImportaPagoSOF.spImportaPagoSOF"
            .CommandType = adCmdStoredProc
            .ActiveConnection = oAccesoDatos.cnn.ObjConexion

            .Parameters.Append .CreateParameter(, adDate, adParamInput, 30)  'Fecha de Pago
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 100)  'Referencia
            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Monto
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Empresa
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Cuenta Bancaria
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Usuario
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Identificador
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Periodo
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Operacion (Insert,Update o Delete)

            .Parameters.Append .CreateParameter(, adVarChar, adParamOutput, 200)  'Resultado de la ejecución del SP

            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Monto de la cancelacion
            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'RenExcel
            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Renglon
            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'NoPagos
            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Id Importacion  'AMGM 01NOV2009

            .Parameters.Append .CreateParameter(, adNumeric, adParamOutput, 2)
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Moneda     'AMGM 2015 PLD


            .Parameters(0) = Format(dpFechaPago.Value, "YYYY/MM/DD") & Format(Time, " hh:mm:ss")
            .Parameters(1) = sReferencia & IIf(bIdentifica, "I", BancomerDD())
            .Parameters(2) = txtCantidad.Text
            .Parameters(3) = cbEmpresa.Text
            .Parameters(4) = Mid(cbCuentaBancaria.Text, 1, 2)
            .Parameters(5) = sUsuarioApp
            .Parameters(6) = sIdentificador
            .Parameters(7) = 1
            .Parameters(8) = "I"
            .Parameters(10) = 0
            .Parameters(11) = txtCantidad.Text
            .Parameters(12) = Null
            .Parameters(13) = Null
            .Parameters(14) = Null
            .Parameters(16) = "MN"   ' AMGM 2015 parametro Moneda por temas de PLD

        End With
        acmd.Execute
        'MsgBox "Resultado = " & acmd.Parameters(9)

        If acmd.Parameters(9) <> 1 Then
                Screen.MousePointer = vbDefault
                MsgBox "La aplicación no pudo realizar el pago..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                Screen.MousePointer = vbHourglass
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
                oAccesoDatos.cnn.AceptarTrans
                Exit Function
        End If

        If acmd.Parameters(15) <> 0 And acmd.Parameters(15) <> 1 And acmd.Parameters(15) <> 6 Then
            '<< AMGM 25JUL2007
              '-----   Obtenemos el resultado del proceso de importación del pago   -----
            sCadenaSQL = ""
            sCadenaSQL = sCadenaSQL & "select /*+RULE*/ * " & vbNewLine
            sCadenaSQL = sCadenaSQL & "from   res_impor " & vbNewLine
            sCadenaSQL = sCadenaSQL & "where  cdgem         = '" & cbEmpresa.Text & "' " & vbNewLine
            sCadenaSQL = sCadenaSQL & "and    fechapago     = '" & Format(dpFechaPago.Value, "DD/MM/YYYY") & "' " & vbNewLine
            sCadenaSQL = sCadenaSQL & "and    referencia    = '" & sReferencia & BancomerDD() & "' " & vbNewLine
            sCadenaSQL = sCadenaSQL & "and    ctabancaria   = '" & Mid(cbCuentaBancaria.Text, 1, 2) & "' " & vbNewLine
            sCadenaSQL = sCadenaSQL & "and    identificador = '" & sIdentificador & "' "
            sCadenaSQL = sCadenaSQL & "and    msgresul     like 'Resultado de la validación:%' "   'AMGM 25JUL2007

        Else

            sCadenaSQL = ""
            sCadenaSQL = sCadenaSQL & "SELECT " & CStr(acmd.Parameters(15)) & " AS VALIDACION, " & vbNewLine
            sCadenaSQL = sCadenaSQL & "SYSDATE AS FECHACARGA, " & vbNewLine
            sCadenaSQL = sCadenaSQL & "NULL AS SECUEIM, " & vbNewLine
            sCadenaSQL = sCadenaSQL & "NULL AS SECUEMP, " & vbNewLine
            sCadenaSQL = sCadenaSQL & "NULL AS secuepdi, " & vbNewLine
            sCadenaSQL = sCadenaSQL & "'Pago importado con éxito.' AS MSGRESUL " & vbNewLine
            sCadenaSQL = sCadenaSQL & "FROM DUAL "

        End If

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0  '-----   La consulta no retorno registros.    -----
                Screen.MousePointer = vbDefault
                MsgBox "No fue posible obtener el resultado de la validación" & vbNewLine & vbNewLine & "debe consultar al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                Screen.MousePointer = vbHourglass
                Screen.MousePointer = vbDefault
                'Unload Me
            Case 1  '-----   Hay registros.                       -----
                MsgBox Replace(Replace(Replace(Replace(oRstPago.ObjSetRegistros.Fields("msgresul").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", ""), vbInformation + vbOKOnly, TITULO_MENSAJE
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                Screen.MousePointer = vbDefault
                MsgBox "No fue posible obtener el resultado de la validación" & vbNewLine & vbNewLine & "debe consultar al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                Screen.MousePointer = vbHourglass
                bCerrarForm = True
                Screen.MousePointer = vbDefault
                oAccesoDatos.cnn.AceptarTrans
                Unload Me
        End Select

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        
        If bIdentifica = True And acmd.Parameters(9) = 1 And (acmd.Parameters(15) = 0 Or acmd.Parameters(15) = 1 Or acmd.Parameters(15) = 6) Then
            sCadenaSQL = "spIdentificaPagos('" & cbEmpresa.Text & "','" & txtGrupo.Text & "','" & Mid(cmbCiclo.Text, 1, 2) & "','G','" & Format(dpFechaPago.Value, "YYYY/MM/DD") & "','" & sSecuencia & "','" & sUsuarioApp & "','" & sReferencia & BancomerDD() & "','" & Format(fdPago, "YYYY/MM/DD") & "', " & txtCantidad.Text & ",'" & Mid(cbCuentaBancaria.Text, 1, 2) & "')"
            oAccesoDatos.cnn.Ejecutar sCadenaSQL
            
            bResIdenPago = True
        End If

    oAccesoDatos.cnn.AceptarTrans

    EjecutarImportacion = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    oAccesoDatos.cnn.DeshacerTrans
    MensajeError Err
End Function
    


Private Sub optPagoGL_Click()
    txtGrupo.Enabled = True
    cmbCiclo.Enabled = False
    cmbCiclo.Clear
    'DPFechaPago.Value = Date
    dpFechaPago.Enabled = False
    txtCantidad.Enabled = False
    'txtCantidad.Text = ""
    txtGrupo.Text = ""
    txtNomGrupo.Text = ""
    'lbBanco = ""
    cbCuentaBancaria.Enabled = False
    'cbCuentaBancaria.ListIndex = 0
    lblMensaje = "GARANTIA"
    lblMensaje.ForeColor = &HFF7070
    cmdImportacion.Enabled = True
    
    If Not bIdentifica Then
        dpFechaPago.Value = Date
        txtCantidad.Text = ""
        cbCuentaBancaria.ListIndex = 0
    End If
    
End Sub

Private Sub optPagoMC_Click()
    txtGrupo.Enabled = True
    cmbCiclo.Enabled = False
    cmbCiclo.Clear
    'DPFechaPago.Value = Date
    dpFechaPago.Enabled = False
    txtCantidad.Enabled = False
    'txtCantidad.Text = ""
    txtGrupo.Text = ""
    txtNomGrupo.Text = ""
    'lbBanco = ""
    cbCuentaBancaria.Enabled = False
    'cbCuentaBancaria.ListIndex = 0
    lblMensaje = "PAGO"
    lblMensaje.ForeColor = &H8000&
    cmdImportacion.Enabled = True
    
    If Not bIdentifica Then
        dpFechaPago.Value = Date
        txtCantidad.Text = ""
        cbCuentaBancaria.ListIndex = 0
    End If
    
End Sub

Private Sub txtCantidad_KeyPress(KeyAscii As Integer)
    If KeyAscii = 13 Then
        cmdImportacion.Enabled = True
    End If
    
    If ((KeyAscii < 48 Or KeyAscii > 57) And KeyAscii <> 8 And KeyAscii <> 46) Then
        KeyAscii = 0
    End If

    If Len(txtCantidad.Text) > 2 Then
      If Mid(txtCantidad.Text, Len(txtCantidad.Text) - 2, 1) = "." And InStr(txtCantidad.Text, ".") > 0 And KeyAscii <> 8 Then
        KeyAscii = 0
      End If
    End If
    
End Sub


Private Sub txtGrupo_KeyPress(KeyAscii As Integer)
Dim oRstPago As New clsoAdoRecordset
Dim sImportarSQL As String

    If KeyAscii = 8 Then
        cmbCiclo.Enabled = False
        cmbCiclo.Clear
        dpFechaPago.Enabled = False
        dpFechaPago.Enabled = True
        txtCantidad.Enabled = False
        cbCuentaBancaria.Enabled = False
        txtNomGrupo.Text = ""
        cmdImportacion.Enabled = True
        
        If Not bIdentifica Then
            dpFechaPago.Value = Date
            txtCantidad.Text = ""
            cbCuentaBancaria.ListIndex = 0
            lbBanco = ""
        End If
        
        bCargaCiclos = False
    End If
    
    If KeyAscii = 13 And Not bCargaCiclos Then
   
        sImportarSQL = ""
        sImportarSQL = "SELECT CICLO, NOMBRE NOMBRE, CDGTPC,INICIO FROM PRN, NS "
        sImportarSQL = sImportarSQL & "WHERE PRN.CDGEM = NS.CDGEM "
        sImportarSQL = sImportarSQL & "AND PRN.CDGNS = NS.CODIGO "
        sImportarSQL = sImportarSQL & "AND PRN.CDGEM = '" & cbEmpresa.Text & "' "
        sImportarSQL = sImportarSQL & "AND PRN.CDGNS = '" & txtGrupo.Text & "' "
        sImportarSQL = sImportarSQL & "AND PRN.SITUACION = 'E' "

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0 '-----   La consulta no retorno registros.   -----
                If optPagoGL Then
                    sImportarSQL = ""
                    sImportarSQL = "SELECT MAX(CICLO) CICLO FROM SN "
                    sImportarSQL = sImportarSQL & "WHERE SN.CDGEM = '" & cbEmpresa.Text & "' "
                    sImportarSQL = sImportarSQL & "AND SN.CDGNS = '" & txtGrupo.Text & "' "
                    sImportarSQL = sImportarSQL & "AND SN.CICLO NOT LIKE 'D%' AND SITUACION <> 'R'"
                
                    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                    oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
                    Select Case oRstPago.HayRegistros
                        Case 0 '-----   La consulta no retorno registros.   -----
                            MsgBox "No hay información para el grupo " & txtGrupo.Text & ". Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                            oRstPago.Cerrar
                            Screen.MousePointer = vbDefault
                            Exit Sub
                       Case 1 '-----   Hay registros.                       -----
                       
                            sImportarSQL = ""
                            sImportarSQL = "SELECT CICLO, NOMBRE, CDGTPC,INICIO FROM SN, NS "
                            sImportarSQL = sImportarSQL & "WHERE SN.CDGEM = NS.CDGEM "
                            sImportarSQL = sImportarSQL & "AND SN.CDGNS = NS.CODIGO AND SITUACION <> 'R' "
                            sImportarSQL = sImportarSQL & "AND SN.CDGEM = '" & cbEmpresa.Text & "' "
                            sImportarSQL = sImportarSQL & "AND SN.CDGNS = '" & txtGrupo.Text & "' "
                            sImportarSQL = sImportarSQL & "AND SN.CICLO = '" & oRstPago.ObjSetRegistros.Fields("ciclo").Value & "' "
                            
                            If IsNull(oRstPago.ObjSetRegistros.Fields("ciclo").Value) Then
                                MsgBox "El grupo no tiene crédito activo. Favor de verificar", vbCritical + vbOKOnly, TITULO_MENSAJE
                                oRstPago.Cerrar
                                Screen.MousePointer = vbDefault
                                Exit Sub
                            End If
                            
                            If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                            oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
                       
                           If oRstPago.NumeroRegistros > 1 Then
                               cmbCiclo.Enabled = True
                           End If
                       
                           While Not oRstPago.FinDeArchivo
                               cmbCiclo.AddItem oRstPago.ObjSetRegistros.Fields("ciclo").Value & "      " & oRstPago.ObjSetRegistros.Fields("inicio").Value & "      " & oRstPago.ObjSetRegistros.Fields("cdgtpc").Value
                               txtNomGrupo.Text = oRstPago.ObjSetRegistros.Fields("nombre").Value
                               oRstPago.IrAlRegSiguiente
                           Wend
                           
                           bCargaCiclos = True
                           
                           If oRstPago.NumeroRegistros = 1 Then
                               cmbCiclo.Enabled = False
                               cmbCiclo.ListIndex = 0
            
                           End If
                           
                           If Not bIdentifica Then
                                'DPFechaPago.Enabled = False  'DESCOMENTAR ESTA LINEA PARA BLOQUEAR FECHA VALOR AMGM 18MAY2011
                                dpFechaPago.Enabled = True
                                txtCantidad.Enabled = True
                                cbCuentaBancaria.Enabled = True
                            End If
                       
                       Case 2  '-----   El Query no se pudo ejecutar.        -----
                           MsgBox "La aplicación no pudo obtener la información del grupo " & txtGrupo.Text & "." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                           oRstPago.Cerrar
                           Screen.MousePointer = vbDefault
                           Exit Sub
                   End Select
               Else
                    MsgBox "No hay información para el grupo " & txtGrupo.Text & ". Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oRstPago.Cerrar
                    Screen.MousePointer = vbDefault
                    Exit Sub
               End If
                
            Case 1 '-----   Hay registros.                       -----
            
                If oRstPago.NumeroRegistros > 1 Then
                    cmbCiclo.Enabled = True
                End If
            
                While Not oRstPago.FinDeArchivo
                    cmbCiclo.AddItem oRstPago.ObjSetRegistros.Fields("ciclo").Value & "      " & oRstPago.ObjSetRegistros.Fields("inicio").Value & "      " & oRstPago.ObjSetRegistros.Fields("cdgtpc").Value
                    txtNomGrupo.Text = oRstPago.ObjSetRegistros.Fields("nombre").Value
                    oRstPago.IrAlRegSiguiente
                Wend
                
                bCargaCiclos = True
                
                If oRstPago.NumeroRegistros = 1 Then
                    cmbCiclo.Enabled = False
                    cmbCiclo.ListIndex = 0
 
                End If
                
                If Not bIdentifica Then
                     '---RETA-HABILITAR CAMBIO PARA VERSION ADMINCREDV23 DEL 09022011-------'
                     dpFechaPago.Enabled = True
                     'DPFechaPago.Enabled = False
                     '----------------------------------------------------------------------'
                     txtCantidad.Enabled = True
                     cbCuentaBancaria.Enabled = True
                 End If
            
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "La aplicación no pudo obtener la información del grupo " & txtGrupo.Text & "." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
                Exit Sub
        End Select
    End If


    If ((KeyAscii < 48 Or KeyAscii > 57) And KeyAscii <> 8) Then
        KeyAscii = 0
    End If
End Sub


Public Function BancomerDD() As String
    Dim Referencia(9) As String
    Dim dd As Integer
    Dim ponderadores1(9) As Integer
    Dim sumatoria As Long
    Dim res As Integer
    Dim strRes As String
    Dim i As Integer
    Dim esDecimal As String
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    BancomerDD = ""
    
    ponderadores1(1) = 2
    ponderadores1(2) = 1
    ponderadores1(3) = 2
    ponderadores1(4) = 1
    ponderadores1(5) = 2
    ponderadores1(6) = 1
    ponderadores1(7) = 2
    ponderadores1(8) = 1
    ponderadores1(9) = 2
    
    For i = 1 To 9
        If i = 1 And Mid(sReferencia, i, 1) = "P" Then   'AMGM 16MAY2011 CAMBIO PARA REFERENCAS BANORTE QUE EMPIEZAN CON "P"
            Referencia(i) = "7"
        Else
            Referencia(i) = Mid(sReferencia, i, 1)
        End If
    Next i
    
    sumatoria = 0
    res = 0
    
    For i = 1 To 9
        res = Referencia(i) * ponderadores1(i)
        strRes = CStr(res)
        
        If Len(strRes) > 1 Then
            res = Val(Mid(strRes, 1, 1)) + Val(Mid(strRes, 2, 1))
        End If
        
        sumatoria = sumatoria + res
    Next i
    
    esDecimal = CStr(sumatoria / 10)
    
    If Len(esDecimal) > 1 Then
        dd = Val(sumatoria) Mod 10
        dd = Val(sumatoria) + (10 - dd)
        dd = dd - Val(sumatoria)
    Else
        dd = 0
    End If

    BancomerDD = dd
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    Screen.MousePointer = vbHourglass
    MsgBox "Se ha generado el Error no: " & CStr(Err.Number) & vbNewLine & "Descripción: " & Err.Description, vbCritical + vbOKOnly, "Error en la Aplicación"
    Screen.MousePointer = vbDefault
End Function

Private Sub txtGrupo_LostFocus()
    txtGrupo_KeyPress (13)
End Sub


