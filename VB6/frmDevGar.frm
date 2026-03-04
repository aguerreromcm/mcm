VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmDevGar 
   AutoRedraw      =   -1  'True
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Importación de Pagos"
   ClientHeight    =   6735
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9690
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
   ScaleHeight     =   6735
   ScaleWidth      =   9690
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox pbContImportacion 
      Align           =   1  'Align Top
      BackColor       =   &H00FFF9F9&
      Height          =   5685
      Left            =   0
      ScaleHeight     =   5625
      ScaleWidth      =   9630
      TabIndex        =   7
      Top             =   735
      Width           =   9690
      Begin VB.ComboBox cmbMotDev 
         Height          =   315
         Left            =   2160
         TabIndex        =   27
         Top             =   2520
         Width           =   7215
      End
      Begin MSFlexGridLib.MSFlexGrid fgDetalleMovs 
         Height          =   1695
         Left            =   240
         TabIndex        =   23
         Top             =   3360
         Width           =   9255
         _ExtentX        =   16325
         _ExtentY        =   2990
         _Version        =   393216
      End
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
         Left            =   2040
         MaxLength       =   9
         TabIndex        =   19
         Top             =   2160
         Width           =   1575
      End
      Begin VB.ComboBox cmbCiclo 
         Height          =   315
         Left            =   840
         TabIndex        =   17
         Top             =   1200
         Visible         =   0   'False
         Width           =   615
      End
      Begin VB.TextBox txtNomGrupo 
         BackColor       =   &H8000000F&
         Enabled         =   0   'False
         Height          =   285
         Left            =   3480
         TabIndex        =   16
         Top             =   720
         Width           =   5895
      End
      Begin VB.TextBox txtGrupo 
         Height          =   285
         Left            =   840
         MaxLength       =   6
         TabIndex        =   15
         Top             =   720
         Width           =   975
      End
      Begin VB.CommandButton cmdImportacion 
         Caption         =   "&Aplicar Dev."
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   375
         Left            =   3600
         TabIndex        =   10
         Top             =   5160
         Width           =   1695
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
         Left            =   8400
         TabIndex        =   9
         Top             =   5160
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
         ItemData        =   "frmDevGar.frx":0000
         Left            =   1080
         List            =   "frmDevGar.frx":0007
         Style           =   2  'Dropdown List
         TabIndex        =   8
         Top             =   210
         Width           =   1395
      End
      Begin MSComCtl2.DTPicker DPFechaPago 
         Height          =   300
         Left            =   2040
         TabIndex        =   11
         Top             =   1710
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
      Begin VB.Label Label13 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Motivo de la Devolución:"
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
         TabIndex        =   26
         Top             =   2640
         Width           =   1935
      End
      Begin VB.Label Label3 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Detalle de Movimientos:"
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
         Top             =   3000
         Width           =   2055
      End
      Begin VB.Label lblMensaje 
         Alignment       =   2  'Center
         BackColor       =   &H00FFF9F9&
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   615
         Left            =   1800
         TabIndex        =   22
         Top             =   1200
         Width           =   7575
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
         Left            =   1920
         TabIndex        =   21
         Top             =   780
         Width           =   1575
      End
      Begin VB.Label Label9 
         BackColor       =   &H00FFF9F9&
         Caption         =   "Cantidad a Devolver:"
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
         TabIndex        =   20
         Top             =   2205
         Width           =   1815
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
         TabIndex        =   18
         Top             =   1305
         Visible         =   0   'False
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
         TabIndex        =   14
         Top             =   780
         Width           =   735
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
         TabIndex        =   13
         Top             =   285
         Width           =   720
      End
      Begin VB.Label Label12 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Fecha de Devolución:"
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
         TabIndex        =   12
         Top             =   1800
         Width           =   1620
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
      ScaleWidth      =   9690
      TabIndex        =   3
      Top             =   0
      Width           =   9690
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   360
         Picture         =   "frmDevGar.frx":0013
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   28
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
         Caption         =   "Módulo de Devolución de Garantias"
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
         Width           =   5130
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
      Left            =   5760
      Top             =   6120
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   0
      Top             =   6450
      Width           =   9690
      _ExtentX        =   17092
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   7056
            MinWidth        =   7056
            Text            =   "Módulo de Devolución de Garantias"
            TextSave        =   "Módulo de Devolución de Garantias"
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
   Begin VB.Label Label2 
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
      Left            =   0
      TabIndex        =   24
      Top             =   0
      Width           =   1935
   End
End
Attribute VB_Name = "frmDevGar"
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



Private Sub cmbCiclo_Click()
    Dim oRstPago As New clsoAdoRecordset
    
    Call BorrarFilasGrids
    
    'lblMensaje = "El saldo de garantía líquida para el ciclo " & Mid(cmbCiclo.Text, 1, 2) & " es: " & Format(Mid(cmbCiclo.Text, (InStr(cmbCiclo.Text, "|") + 1), Len(cmbCiclo.Text) - InStr(cmbCiclo.Text, "|")), "$###,###,###,##0.00")
    
    'CAMBIO PARA LA CUBETA UNICA DE GARANTIAS
    lblMensaje = "El saldo de garantía líquida es: " & Format(Mid(cmbCiclo.Text, (InStr(cmbCiclo.Text, "|") + 1), Len(cmbCiclo.Text) - InStr(cmbCiclo.Text, "|")), "$###,###,###,##0.00")
    lblMensaje.ForeColor = vbBlack
    
    
        sImportarSQL = ""
        sImportarSQL = "SELECT G.CDGCLNS GRUPO,G.CICLO, G.FPAGO FECHAMOV, G.CDGCB CTABANC, "
        sImportarSQL = sImportarSQL & "G.REFERENCIA, "
        sImportarSQL = sImportarSQL & "G.CANTIDAD, "
        sImportarSQL = sImportarSQL & "(C.DESCRIPCION || (SELECT CASE WHEN FDEPOSITO IS NOT NULL THEN "
        sImportarSQL = sImportarSQL & "' (DEPOSITO REALIZADO EL ' || TO_CHAR(FDEPOSITO,'DD/MM/YYYY') || ')' "
        sImportarSQL = sImportarSQL & "ELSE '' "
        sImportarSQL = sImportarSQL & "END "
        sImportarSQL = sImportarSQL & "FROM PDI "
        sImportarSQL = sImportarSQL & "WHERE CDGEM = G.CDGEM "
        sImportarSQL = sImportarSQL & "AND CDGCLNS = G.CDGCLNS "
        sImportarSQL = sImportarSQL & "AND CLNS = G.CLNS "
        sImportarSQL = sImportarSQL & "AND SECUENCIAIM = G.SECPAGO "
        sImportarSQL = sImportarSQL & "AND CDGCB = G.CDGCB "
        sImportarSQL = sImportarSQL & "AND CANTIDAD = G.CANTIDAD "
        sImportarSQL = sImportarSQL & "AND FECHAIM = G.FPAGO)) DESCRIPCION "
        sImportarSQL = sImportarSQL & "FROM PAG_GAR_SIM G, CATMOVSGARSIMPLE C "
        sImportarSQL = sImportarSQL & "Where G.ESTATUS = C.Codigo "
        sImportarSQL = sImportarSQL & "AND G.CDGEM = '" & cbEmpresa.Text & "' "
        sImportarSQL = sImportarSQL & "AND G.CDGCLNS = '" & txtGrupo.Text & "' "
        'sImportarSQL = sImportarSQL & "AND G.CICLO = '" & Mid(cmbCiclo.Text, 1, 2) & "' "   'CAMBIO PARA LA CUBETA UNICA DE GARANTIAS
        sImportarSQL = sImportarSQL & "AND G.ESTATUS <> 'CA' ORDER BY G.FPAGO ASC, G.SECPAGO"

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0 '-----   La consulta no retorno registros.   -----
                    MsgBox "No hay información para el grupo " & txtGrupo.Text & " ciclo " & Mid(cmbCiclo.Text, 1, 2) & ". Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oRstPago.Cerrar
                    Screen.MousePointer = vbDefault
                    DPFechaPago.Enabled = False
                    txtCantidad.Enabled = False
                    cmdImportacion.Enabled = True
                    Call BorrarFilasGrids
                    cmbMotDev.Enabled = False
                    cmbMotDev.ListIndex = -1
                    'lblMensaje = ""
            Case 1 '-----   Hay registros.                       -----
          
                While Not oRstPago.FinDeArchivo
                    Call CargaMov(oRstPago)
                    oRstPago.IrAlRegSiguiente
                Wend
                
                DPFechaPago.Enabled = False
                txtCantidad.Enabled = True
                cmbMotDev.Enabled = True
         
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "La aplicación no pudo obtener la información del grupo " & txtGrupo.Text & "." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
        End Select

End Sub

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
    

    If cbEmpresa.Text = "" Or txtGrupo.Text = "" Or cmbCiclo.Text = "" Or txtCantidad.Text = "" Or cmbMotDev.Text = "" Then
        sMensaje = ""
        sMensaje = sMensaje & "Favor de verificar que alguno de los siguientes campos no este vacio: " & vbNewLine
        sMensaje = sMensaje & vbTab & "1) Empresa." & vbNewLine
        sMensaje = sMensaje & vbTab & "2) Codigo de Grupo." & vbNewLine
        sMensaje = sMensaje & vbTab & "3) Ciclo." & vbNewLine
        sMensaje = sMensaje & vbTab & "4) Fecha de Pago." & vbNewLine
        sMensaje = sMensaje & vbTab & "5) Cantidad" & vbNewLine
        sMensaje = sMensaje & vbTab & "6) Motivo de Devolución." & vbNewLine
        MsgBox sMensaje, vbCritical + vbOKOnly, TITULO_MENSAJE
        
        Screen.MousePointer = vbDefault
        Exit Sub
    End If

        sFechaCarga = Format(Date, "YYYY/MM/DD")
        sMensaje = ""
        sMensaje = sMensaje & "¿Esta seguro(a) que desea importar el depósito para?" & vbNewLine & vbNewLine
        sMensaje = sMensaje & "Empresa:" & vbTab & vbTab & cbEmpresa.Text & vbNewLine
        sMensaje = sMensaje & "Grupo:" & vbTab & vbTab & txtGrupo.Text & vbNewLine
        sMensaje = sMensaje & "Nombre Grupo:" & vbTab & txtNomGrupo.Text & vbNewLine
        sMensaje = sMensaje & "Ciclo:" & vbTab & vbTab & Mid(cmbCiclo.Text, 1, 2) & vbNewLine
        sMensaje = sMensaje & "Fecha:" & vbTab & vbTab & DPFechaPago.Value & vbNewLine
        sMensaje = sMensaje & "Cantidad:" & vbTab & vbTab & txtCantidad.Text & vbNewLine
        sMensaje = sMensaje & "Mot. Dev.:" & vbTab & cmbMotDev.Text & vbNewLine
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
            DPFechaPago.Value = Date
            cbEmpresa.ListIndex = 0
            cmbCiclo.Enabled = False
            DPFechaPago.Enabled = False
            txtCantidad.Enabled = False
            cmbCiclo.Clear
            bCargaCiclos = False
            txtCantidad.Text = ""
            txtGrupo.Text = ""
            txtNomGrupo.Text = ""
            Call BorrarFilasGrids
            cmbMotDev.ListIndex = -1
            lblMensaje = ""
            cmbMotDev.Enabled = False
            

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
Dim l As Integer, i As Integer

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call InicializarGrids
    Call LlenaComboMotDev

    bCerrarForm = False
    cmdImportacion.Visible = True
    cmdImportacion.Enabled = True
    DPFechaPago.Value = Date
    sbBarraEstado.Panels(1).Text = TITULO_MOD_DEVGAR
    cbEmpresa.ListIndex = 0
    txtGrupo.Enabled = True
    cmbCiclo.Enabled = False
    DPFechaPago.Enabled = False   '--- CAMBIO 07/10/2010 --- JOSE ANTONIO RENDON TORRES --- VALIDAR CONTRA FECHA SERVER --- INICIO ---'
    txtCantidad.Enabled = False

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

    If CDbl(txtCantidad.Text) > CDbl(Mid(cmbCiclo.Text, (InStr(cmbCiclo.Text, "|") + 1), Len(cmbCiclo.Text) - InStr(cmbCiclo.Text, "|"))) Then
        MsgBox "El saldo de la Garantía Líquida es menor al monto de la devolución. Favor de verificar", vbCritical + vbOKOnly, TITULO_MENSAJE
        Exit Function
    End If
    
        sReferencia = "0" & txtGrupo.Text & Mid(cmbCiclo.Text, (InStr(cmbCiclo.Text, "*") + 1), 2)

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
        
        Set acmd = Nothing
        With acmd

            .CommandText = "spMovsGarantiasSIMPLE"
            .CommandType = adCmdStoredProc
            .ActiveConnection = oAccesoDatos.cnn.ObjConexion

            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Tipo de Movimiento
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Empresa
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Grupo
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Referencia
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Tipo Cte
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Ciclo
            .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Monto
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Cta. Banc.
            .Parameters.Append .CreateParameter(, adDate, adParamInput, 30)  'Fecha de Pago
            .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Usuario

            .Parameters.Append .CreateParameter(, adVarChar, adParamOutput, 200)  'Resultado de la ejecución del SP

            .Parameters(0) = Mid(cmbMotDev.Text, 1, 2)
            .Parameters(1) = cbEmpresa.Text
            .Parameters(2) = txtGrupo.Text
            .Parameters(3) = sReferencia & BancomerDD()
            .Parameters(4) = "G"
            .Parameters(5) = Mid(cmbCiclo.Text, 1, 2)
            .Parameters(6) = txtCantidad.Text
            .Parameters(7) = "12"
            .Parameters(8) = Format(DPFechaPago.Value, "YYYY/MM/DD")
            .Parameters(9) = sUsuarioApp

        End With
        acmd.Execute
        'MsgBox "Resultado = " & acmd.Parameters(9)

        If Mid(acmd.Parameters(10), 1, 1) = "1" Then
            MsgBox "Devolución aplicada correctamente.", vbOKOnly, TITULO_MENSAJE
        Else
            MsgBox "Error al aplicar la devolucion:" & Mid(acmd.Parameters(10), 2, Len(acmd.Parameters(10))) & vbNewLine & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
        End If
        oRstPago.Cerrar

        oAccesoDatos.cnn.AceptarTrans

    EjecutarImportacion = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    oAccesoDatos.cnn.DeshacerTrans
    MensajeError Err
End Function
    





'Private Sub optPagoGL_Click()
'    txtGrupo.Enabled = True
'    cmbCiclo.Enabled = False
'    cmbCiclo.Clear
'    'DPFechaPago.Value = Date
'    DPFechaPago.Enabled = False
'    txtCantidad.Enabled = False
'    'txtCantidad.Text = ""
'    txtGrupo.Text = ""
'    txtNomGrupo.Text = ""
'    'lbBanco = ""
'    cbCuentaBancaria.Enabled = False
'    'cbCuentaBancaria.ListIndex = 0
'    lblMensaje = "GARANTIA"
'    lblMensaje.ForeColor = &HFF7070
'    cmdImportacion.Enabled = True
'
'    If Not bIdentifica Then
'        DPFechaPago.Value = Date
'        txtCantidad.Text = ""
'        cbCuentaBancaria.ListIndex = 0
'    End If
'
'End Sub

'Private Sub optPagoMC_Click()
'    txtGrupo.Enabled = True
'    cmbCiclo.Enabled = False
'    cmbCiclo.Clear
'    'DPFechaPago.Value = Date
'    DPFechaPago.Enabled = False
'    txtCantidad.Enabled = False
'    'txtCantidad.Text = ""
'    txtGrupo.Text = ""
'    txtNomGrupo.Text = ""
'    'lbBanco = ""
'    cbCuentaBancaria.Enabled = False
'    'cbCuentaBancaria.ListIndex = 0
'    lblMensaje = "PAGO"
'    lblMensaje.ForeColor = &H8000&
'    cmdImportacion.Enabled = True
'
'    If Not bIdentifica Then
'        DPFechaPago.Value = Date
'        txtCantidad.Text = ""
'        cbCuentaBancaria.ListIndex = 0
'    End If
'
'End Sub


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


    If KeyAscii = 8 Or Len(txtGrupo.Text) < 6 Then
        cmbCiclo.Enabled = False
        cmbCiclo.Clear
        DPFechaPago.Enabled = False
        txtCantidad.Enabled = False
        txtNomGrupo.Text = ""
        cmdImportacion.Enabled = True
        Call BorrarFilasGrids
        lblMensaje = ""
        cmbMotDev.Enabled = False
        cmbMotDev.ListIndex = -1
        
        bCargaCiclos = False
    End If
    
    If KeyAscii = 13 And Not bCargaCiclos Then
   
        sImportarSQL = ""
'        sImportarSQL = "SELECT CICLO, NOMBRE, CDGTPC,INICIO, "
'        sImportarSQL = sImportarSQL & "NVL(fnSdoGarantia('" & cbEmpresa.Text & "','" & txtGrupo.Text & "',CICLO,'G'),0) SALDOGL  "
'        sImportarSQL = sImportarSQL & "From "
'        sImportarSQL = sImportarSQL & "( "
'        sImportarSQL = sImportarSQL & "SELECT CICLO, NOMBRE, CDGTPC,INICIO FROM PRN, NS "
'        sImportarSQL = sImportarSQL & "WHERE PRN.CDGEM = NS.CDGEM "
'        sImportarSQL = sImportarSQL & "AND PRN.CDGNS = NS.CODIGO "
'        sImportarSQL = sImportarSQL & "AND PRN.CDGEM = '" & cbEmpresa.Text & "' "
'        sImportarSQL = sImportarSQL & "AND PRN.CDGNS = '" & txtGrupo.Text & "' "
'        sImportarSQL = sImportarSQL & "AND PRN.SITUACION IN ('E','L') "
'        sImportarSQL = sImportarSQL & "ORDER BY INICIO DESC "
'        sImportarSQL = sImportarSQL & ") "
'        sImportarSQL = sImportarSQL & "WHERE ROWNUM <= 2 "
'        sImportarSQL = sImportarSQL & "ORDER BY 1 "

'        CAMBIO PARA CONSIDERAR LA CUBETA UNICA DE GARANTIAS
        sImportarSQL = "SELECT CICLO, NOMBRE,CDGTPC,INICIO, "
        sImportarSQL = sImportarSQL & "NVL(fnsdogarantia(SN.CDGEM,SN.CDGNS,SN.CICLO,'G'),0) SALDOGL  "
        sImportarSQL = sImportarSQL & "FROM SN, NS "
        sImportarSQL = sImportarSQL & "WHERE SN.CDGEM = NS.CDGEM "
        sImportarSQL = sImportarSQL & "AND SN.CDGNS = NS.CODIGO "
        sImportarSQL = sImportarSQL & "AND SN.CDGEM = '" & cbEmpresa.Text & "' "
        sImportarSQL = sImportarSQL & "AND SN.CDGNS = '" & txtGrupo.Text & "' "
        sImportarSQL = sImportarSQL & "AND SN.CICLO = ( "
        sImportarSQL = sImportarSQL & "SELECT MAX(CICLO) FROM SN WHERE SN.CDGEM = '" & cbEmpresa.Text & "' "
        sImportarSQL = sImportarSQL & "AND SN.CDGNS = '" & txtGrupo.Text & "' AND SITUACION <> 'R'"
        sImportarSQL = sImportarSQL & ") AND SN.SITUACION IN ('A','S')"

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0 '-----   La consulta no retorno registros.   -----
                    MsgBox "No hay información para el grupo " & txtGrupo.Text & ". Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oRstPago.Cerrar
                    Screen.MousePointer = vbDefault
            Case 1 '-----   Hay registros.                       -----
            
                If oRstPago.NumeroRegistros > 1 Then
                    cmbCiclo.Enabled = True
                End If
            
                While Not oRstPago.FinDeArchivo
                    cmbCiclo.AddItem oRstPago.ObjSetRegistros.Fields("ciclo").Value & "      " & oRstPago.ObjSetRegistros.Fields("inicio").Value & "      *" & oRstPago.ObjSetRegistros.Fields("cdgtpc").Value & "      |" & oRstPago.ObjSetRegistros.Fields("saldogl").Value
                    txtNomGrupo.Text = oRstPago.ObjSetRegistros.Fields("nombre").Value
                    oRstPago.IrAlRegSiguiente
                Wend
                
                bCargaCiclos = True
                Call BorrarFilasGrids
                '---RETA-HABILITAR CAMBIO PARA VERSION ADMINCREDV23 DEL 09022011-------'
                DPFechaPago.Enabled = True
                'DPFechaPago.Enabled = False
                '----------------------------------------------------------------------'
                
                If oRstPago.NumeroRegistros = 1 Then
                    cmbCiclo.Enabled = False
                    cmbCiclo.ListIndex = 0
                    '---RETA-HABILITAR CAMBIO PARA VERSION ADMINCREDV23 DEL 09022011-------'
                    DPFechaPago.Enabled = True
                    'DPFechaPago.Enabled = False
                    '----------------------------------------------------------------------'
                End If
          
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "La aplicación no pudo obtener la información del grupo " & txtGrupo.Text & "." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
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
        Referencia(i) = Mid(sReferencia, i, 1)
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

Private Sub CargaMov(ByVal poRst As clsoAdoRecordset)
    Dim sFechaCarga As String, vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    'sFechaCarga = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
    With Me.fgDetalleMovs
        .Rows = .Rows + 1
        .Row = .Rows - 1
        
        vColorFrente = vbBlack
        If (.Row Mod 2 = 0) Then
            vColorFondo = &HF0FFF0
        Else
            vColorFondo = vbWhite
        End If
        
        .Col = 0
        .CellAlignment = flexAlignRightCenter
        .Text = CStr(.Row) & " "
        
        .Col = 1
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fechamov").Value), "", poRst.ObjSetRegistros.Fields("fechamov").Value)
        
        .Col = 2
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
        
        .Col = 3
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("ctabanc").Value), "", poRst.ObjSetRegistros.Fields("ctabanc").Value)
        
        .Col = 4
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "", Format(poRst.ObjSetRegistros.Fields("cantidad").Value, "$###,###,###,##0.00"))
        
        .Col = 5
        .CellAlignment = flexAlignLeftCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("descripcion").Value), "", poRst.ObjSetRegistros.Fields("descripcion").Value)
        
    End With
    
'    With Me.fgDetalleMovs
'        .Rows = .Rows + 1
'        .Row = .Rows - 1
'
'        vColorFrente = vbBlack
'        If (.Row Mod 2 = 0) Then
'            vColorFondo = &HF0FFF0
'        Else
'            vColorFondo = vbWhite
'        End If
'
'        .Col = 0
'        .CellAlignment = flexAlignRightCenter
'        .Text = CStr(.Row) & " "
'
'        .Col = 1
'        .CellPictureAlignment = flexAlignCenterCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("grupo").Value), "", poRst.ObjSetRegistros.Fields("grupo").Value)
'
'        .Col = 2
'        .CellAlignment = flexAlignCenterCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("ciclo").Value), "", poRst.ObjSetRegistros.Fields("ciclo").Value)
'
'        .Col = 3
'        .CellAlignment = flexAlignCenterCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fechamov").Value), "", poRst.ObjSetRegistros.Fields("fechamov").Value)
'
'        .Col = 4
'        .CellAlignment = flexAlignCenterCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
'
'        .Col = 5
'        .CellAlignment = flexAlignCenterCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("ctabanc").Value), "", poRst.ObjSetRegistros.Fields("ctabanc").Value)
'
'        .Col = 6
'        .CellAlignment = flexAlignCenterCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "", Format(poRst.ObjSetRegistros.Fields("cantidad").Value, "$###,###,###,##0.00"))
'
'        .Col = 7
'        .CellAlignment = flexAlignLeftCenter
'        .CellForeColor = vColorFrente
'        .CellBackColor = vColorFondo
'        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("descripcion").Value), "", poRst.ObjSetRegistros.Fields("descripcion").Value)
'
'    End With
    
    'lbDatoNoRegsTab1.Caption = CStr(dNoRegs)
    'lbMontoTab1.Caption = Format(dMonto, "$###,###,###,##0.00")
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub InicializarGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    '-----   Inicializamos el Grid para los Movimientos de Garantía Líquida   -----
    With fgDetalleMovs
        .Rows = 1
        .Cols = 6
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 400
        
        .Col = 1
        .TextMatrix(0, 1) = "Fecha Mov."
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 1200
        
        .Col = 2
        .TextMatrix(0, 2) = "Referencia"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 1400
        
        .Col = 3
        .TextMatrix(0, 3) = "Cta. Ban."
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 950
        
        .Col = 4
        .TextMatrix(0, 4) = "Monto"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1500
        
        .Col = 5
        .TextMatrix(0, 5) = "Descripción"
        .ColAlignment(5) = flexAlignLeftCenter
        .ColWidth(5) = 6500
        
    End With
    
'    With fgDetalleMovs
'        .Rows = 1
'        .Cols = 8
'
'        .Col = 0
'        .TextMatrix(0, 0) = "No."
'        .ColAlignment(0) = flexAlignCenterCenter
'        .ColWidth(0) = 400
'
'        .Col = 1
'        .TextMatrix(0, 1) = "Grupo"
'        .ColAlignment(1) = flexAlignCenterCenter
'        .ColWidth(1) = 850
'
'        .Col = 2
'        .TextMatrix(0, 2) = "Ciclo"
'        .ColAlignment(2) = flexAlignCenterCenter
'        .ColWidth(2) = 650
'
'        .Col = 3
'        .TextMatrix(0, 3) = "Fecha Mov."
'        .ColAlignment(3) = flexAlignCenterCenter
'        .ColWidth(3) = 1200
'
'        .Col = 4
'        .TextMatrix(0, 4) = "Referencia"
'        .ColAlignment(4) = flexAlignCenterCenter
'        .ColWidth(4) = 1400
'
'        .Col = 5
'        .TextMatrix(0, 5) = "Cta. Ban."
'        .ColAlignment(5) = flexAlignCenterCenter
'        .ColWidth(5) = 950
'
'        .Col = 6
'        .TextMatrix(0, 6) = "Monto"
'        .ColAlignment(6) = flexAlignCenterCenter
'        .ColWidth(6) = 1500
'
'        .Col = 7
'        .TextMatrix(0, 7) = "Descripción"
'        .ColAlignment(7) = flexAlignLeftCenter
'        .ColWidth(7) = 6500
'
'    End With

    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub BorrarFilasGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    fgDetalleMovs.Rows = 1
    fgDetalleMovs.Refresh
   
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub


Private Sub LlenaComboMotDev()
Dim oRstPago As New clsoAdoRecordset

        sImportarSQL = ""
        sImportarSQL = "SELECT CODIGO,DESCRIPCION FROM CATMOVSGARSIMPLE WHERE CODIGO LIKE '%D%' "

        If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
        oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        Select Case oRstPago.HayRegistros
            Case 0 '-----   La consulta no retorno registros.   -----
                    MsgBox "No hay información para el catalogo de movimientos. " & txtGrupo.Text & ". Favor de verificar." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    oRstPago.Cerrar
                    Screen.MousePointer = vbDefault
            Case 1 '-----   Hay registros.                       -----
            
                While Not oRstPago.FinDeArchivo
                    cmbMotDev.AddItem oRstPago.ObjSetRegistros.Fields("codigo").Value & " - " & oRstPago.ObjSetRegistros.Fields("descripcion").Value
                    oRstPago.IrAlRegSiguiente
                Wend
          
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "La aplicación no pudo obtener la información del catalogo de movimientos " & txtGrupo.Text & "." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oRstPago.Cerrar
                Screen.MousePointer = vbDefault
        End Select
        oRstPago.Cerrar

End Sub

