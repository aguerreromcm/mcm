VERSION 5.00
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmAuditorias 
   BackColor       =   &H00FFF9F9&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de auditorias al sistema"
   ClientHeight    =   9390
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9855
   Icon            =   "frmAuditorias.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   9390
   ScaleWidth      =   9855
   StartUpPosition =   2  'CenterScreen
   Begin VB.ComboBox cbTipoAud 
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
      Left            =   90
      Style           =   2  'Dropdown List
      TabIndex        =   10
      Top             =   1050
      Width           =   4395
   End
   Begin VB.PictureBox pbSelNo 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   6000
      Picture         =   "frmAuditorias.frx":1CFA
      ScaleHeight     =   210
      ScaleWidth      =   210
      TabIndex        =   7
      Top             =   8850
      Visible         =   0   'False
      Width           =   210
   End
   Begin VB.PictureBox pbSel 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   5670
      Picture         =   "frmAuditorias.frx":1FA4
      ScaleHeight     =   210
      ScaleWidth      =   210
      TabIndex        =   6
      Top             =   8850
      Visible         =   0   'False
      Width           =   210
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
      Left            =   8790
      TabIndex        =   5
      Top             =   8730
      Width           =   1000
   End
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9855
      TabIndex        =   0
      Top             =   0
      Width           =   9855
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   240
         Picture         =   "frmAuditorias.frx":224E
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   17
         Top             =   0
         Width           =   1095
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Auditorias al Sistema"
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
         Left            =   1680
         TabIndex        =   3
         Top             =   60
         Width           =   4665
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
         TabIndex        =   2
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
         TabIndex        =   1
         Top             =   90
         Width           =   135
      End
   End
   Begin MSComctlLib.ProgressBar pbarAudSist 
      Height          =   195
      Left            =   5070
      TabIndex        =   8
      Top             =   9180
      Width           =   2355
      _ExtentX        =   4154
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   9
      Top             =   9105
      Width           =   9855
      _ExtentX        =   17383
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de auditorias al sistema"
            TextSave        =   "Módulo de auditorias al sistema"
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
   Begin AdminCred.ctlFiltroConciliacion ctlFiltroConciliacion1 
      Height          =   2535
      Left            =   -30
      TabIndex        =   4
      Top             =   1620
      Width           =   9900
      _ExtentX        =   17463
      _ExtentY        =   4471
   End
   Begin MSFlexGridLib.MSFlexGrid fgAuditoria 
      Height          =   4425
      Left            =   60
      TabIndex        =   12
      Top             =   4230
      Width           =   9735
      _ExtentX        =   17171
      _ExtentY        =   7805
      _Version        =   393216
      BackColorBkg    =   14737632
      AllowUserResizing=   1
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
   Begin VB.Label lbRegsTab1 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "No. de registros:"
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
      Left            =   60
      TabIndex        =   16
      Top             =   8730
      Width           =   1260
   End
   Begin VB.Label lbDatoNoRegsTab1 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "0"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   195
      Left            =   1350
      TabIndex        =   15
      Top             =   8730
      Width           =   120
   End
   Begin VB.Label lbMontoTab1 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "$0.00"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   195
      Left            =   3555
      TabIndex        =   14
      Top             =   8730
      Width           =   540
   End
   Begin VB.Label Label13 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Monto:"
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
      Left            =   2955
      TabIndex        =   13
      Top             =   8730
      Width           =   525
   End
   Begin VB.Line Line2 
      BorderColor     =   &H00FFFFFF&
      BorderWidth     =   2
      X1              =   90
      X2              =   9720
      Y1              =   1470
      Y2              =   1470
   End
   Begin VB.Line Line1 
      BorderColor     =   &H00E0E0E0&
      BorderWidth     =   2
      X1              =   90
      X2              =   9720
      Y1              =   1500
      Y2              =   1500
   End
   Begin VB.Label lbEtqTipoAud 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "&Tipo de auditoria:"
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
      Left            =   90
      TabIndex        =   11
      Top             =   840
      Width           =   1320
   End
End
Attribute VB_Name = "frmAuditorias"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private bCerrarForm As Boolean

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

Private Sub BorrarFilasGrid()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    fgAuditoria.Rows = 1
    fgAuditoria.Refresh
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ctlFiltroConciliacion1_ClickBuscar()
    Dim sCadenaSQL As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    'dNoRegs = 0
    'dMonto = 0
    'sstEliminacion.Tab = 0
    Call BorrarFilasGrid
    'lbDatoNoRegsTab1.Caption = "0"
    'lbMontoTab1.Caption = "$0.00"

    With ctlFiltroConciliacion1
        cmdCerrar.Enabled = False
        'cmdEliminacion.Visible = False
        'cmdSelTodos.Visible = False
        'cmdQuitarSel.Visible = False
        ctlFiltroConciliacion1.Habilitado = False
    
        Call BuscarPagos(.Empresa, .FechaPago, .TipoCliente, .Codigo, .Nombre, .CtaBancaria, cbTipoAud.ListIndex)
        
        pbarAudSist.Value = 0
        pbarAudSist.Visible = False
        'pbarAudSist.Panels(1).Text = "Se encontraron " & lbDatoNoRegsTab1.Caption & " pagos..."
        
        'cmdSelTodos.Visible = True
        'cmdQuitarSel.Visible = True
        'cmdEliminacion.Visible = True
        cmdCerrar.Enabled = True
        ctlFiltroConciliacion1.Habilitado = True
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub BuscarPagos(ByVal psEmpresa As String, ByVal psFechaPago As String, ByVal psTipoCliente As String, ByVal psCodigo As String, ByVal psNombre As String, ByVal psCtaBancaria As String, ByVal plIndAud As Long)
    Dim oRstConciliar As New clsoAdoRecordset
    Dim sCadenaSQL As String
    Dim sCondEmpresa As String, sCondFechaPago As String, sCondTipoCliente As String, sCondCodigo As String, sCondNombre1 As String, sCondNombre2 As String, sCondCtaBancaria As String
    Dim sTipoAud As String
     
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With ctlFiltroConciliacion1
        If (.Empresa = "(Todas)") Then sCondEmpresa = "" Else sCondEmpresa = "and      mp.cdgem = '" & .Empresa & "' " & vbNewLine
        If (.FechaPago = "") Then sCondFechaPago = "" Else sCondFechaPago = "and      frealdep = '" & Format(.FechaPago, "yyyy/mm/dd") & "' " & vbNewLine
        If (.TipoCliente = "(Todos)") Then sCondTipoCliente = "" Else sCondTipoCliente = "and      clns = '" & Mid(.TipoCliente, 1, 1) & "' " & vbNewLine
        If (.Codigo = "") Then sCondCodigo = "" Else sCondCodigo = "and      cdgclns = '" & .Codigo & "' " & vbNewLine
        If (.Nombre = "") Then
            sCondNombre1 = ""
            sCondNombre2 = ""
        Else
            sCondNombre1 = "and      rtrim(ltrim(nombre)) = '" & UCase(.Nombre) & "' " & vbNewLine
            sCondNombre2 = "and      nvl(rtrim(ltrim(b.nombre1 || ' ' || b.nombre2)), '') || ' ' || nvl(rtrim(ltrim(b.primape || ' ' || b.segape)), '') = '" & UCase(.Nombre) & "' " & vbNewLine
        End If
        If (.CtaBancaria = "") Then sCondCtaBancaria = "" Else sCondCtaBancaria = "and      cdgcb = '" & .CtaBancaria & "' " & vbNewLine
    End With
    
    Select Case plIndAud
        Case 0
        Case 1
        Case 2
        Case 3
        Case 4
            sTipoAud = ""
            sTipoAud = sTipoAud & "and      mp.conciliado = 'D' " & vbNewLine
            sTipoAud = sTipoAud & "and      mp.pagadocap is null " & vbNewLine
            sTipoAud = sTipoAud & "and      mp.pagadoint is null " & vbNewLine
            sTipoAud = sTipoAud & "and      mp.pagadorec is null " & vbNewLine
    End Select
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select   mp.cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cdgclns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.clns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         decode(mp.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQL = sCadenaSQL & "                         'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cdgcl, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.ciclo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.periodo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.secuencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.referencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.tipo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.modo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.conciliado, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.estatus, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.actualizarpe, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.secuenciaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.fechaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.pagadocap, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.pagadoint, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.pagadorec, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         nvl(rtrim(ltrim(b.nombre1 || ' ' || b.nombre2)), '') || ' ' || nvl(rtrim(ltrim(b.primape || ' ' || b.segape)), '') nombre " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     mp, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cl b  " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    mp.estatus    = 'B' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      b.cdgem       = mp.cdgem " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      b.codigo      = mp.cdgclns " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      mp.clns       = 'I' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      mp.modo       = 'I' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      length(mp.referencia) = 9 " & vbNewLine
    sCadenaSQL = sCadenaSQL & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre2 & sCondCtaBancaria & sTipoAud
    sCadenaSQL = sCadenaSQL & "union all " & vbNewLine
    sCadenaSQL = sCadenaSQL & "select   mp.cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cdgclns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.clns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         decode(mp.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQL = sCadenaSQL & "                         'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cdgcl, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.ciclo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.periodo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.secuencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.referencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.tipo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.modo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.conciliado, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.estatus, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.actualizarpe, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.secuenciaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.fechaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.pagadocap, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.pagadoint, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         mp.pagadorec, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         rtrim(ltrim(nombre)) nombre " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     mp, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         ns b " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    mp.estatus    = 'B' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      b.cdgem       = mp.cdgem " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      b.codigo      = mp.cdgclns " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      mp.clns       = 'G' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      mp.modo       = 'I' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      length(mp.referencia) = 9 " & vbNewLine
    sCadenaSQL = sCadenaSQL & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre1 & sCondCtaBancaria & sTipoAud
    sCadenaSQL = sCadenaSQL & "order by frealdep, referencia, secuencia"
    
    oRstConciliar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstConciliar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            MsgBox "No se encontraron pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
            oRstConciliar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con los pagos pendientes de conciliar   -----
            pbarAudSist.Value = 0
            pbarAudSist.Max = oRstConciliar.NumeroRegistros
            pbarAudSist.Visible = True
            
            While Not oRstConciliar.FinDeArchivo
                'pbarAudSist.Value = Val(lbDatoNoRegsTab1.Caption)
                'sbBarraEstado.Panels(1).Text = "Obteniendo pago no. " & CStr(lbDatoNoRegsTab1.Caption + 1) & " de " & CStr(oRstConciliar.NumeroRegistros) & "  (" & CStr(Format(((lbDatoNoRegsTab1.Caption + 1) * 100) / oRstConciliar.NumeroRegistros, "##0.00")) & "%)"
                'Call PonerDatosPorEliminar(oRstConciliar)
                oRstConciliar.IrAlRegSiguiente
            Wend
            
            Screen.MousePointer = vbDefault
            'MsgBox "Se encontraron un total de " & lbDatoNoRegsTab1.Caption & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la lista de pagos..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConciliar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    cbTipoAud.AddItem "Créditos en status de 'E' (Entregado) Sin saldo", 0
    cbTipoAud.AddItem "Créditos en status de 'L' (Liquidado) Con saldo", 1
    cbTipoAud.AddItem "Pagos con intereses incorrectos", 2
    cbTipoAud.AddItem "pagos con intereses Sin distribuir", 3
    cbTipoAud.AddItem "Pagos con status de 'D' (Distribuido) Sin distribuir", 4

    cbTipoAud.ListIndex = 4
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    pbarAudSist.Width = sbBarraEstado.Panels(2).Width - 40
    pbarAudSist.Top = sbBarraEstado.Top + 60
    pbarAudSist.Left = sbBarraEstado.Panels(1).Width + 80
    pbarAudSist.Height = sbBarraEstado.Height - 100
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Image1_Click()

End Sub
