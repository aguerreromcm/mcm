VERSION 5.00
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{BDC217C8-ED16-11CD-956C-0000C04E4C0A}#1.1#0"; "TABCTL32.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmConsultaPagos 
   AutoRedraw      =   -1  'True
   BackColor       =   &H00FFFFFF&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Consulta de Pagos"
   ClientHeight    =   10845
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9945
   Icon            =   "frmConsultaPagos.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   10845
   ScaleWidth      =   9945
   StartUpPosition =   2  'CenterScreen
   Begin AdminCred.ctlFiltroConciliacion ctlFiltroConciliacion 
      Height          =   2535
      Left            =   30
      TabIndex        =   9
      Top             =   750
      Width           =   9900
      _ExtentX        =   17463
      _ExtentY        =   4471
   End
   Begin TabDlg.SSTab SSTPagos 
      Height          =   6195
      Left            =   30
      TabIndex        =   7
      Top             =   3360
      Width           =   9855
      _ExtentX        =   17383
      _ExtentY        =   10927
      _Version        =   393216
      Tabs            =   2
      TabsPerRow      =   2
      TabHeight       =   520
      BackColor       =   16777215
      BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      TabCaption(0)   =   "Consulta de Pagos de (EMPFIN)"
      TabPicture(0)   =   "frmConsultaPagos.frx":1762
      Tab(0).ControlEnabled=   -1  'True
      Tab(0).Control(0)=   "fgPagosFINFIN"
      Tab(0).Control(0).Enabled=   0   'False
      Tab(0).ControlCount=   1
      TabCaption(1)   =   "Consulta de Pagos de (Otros)"
      TabPicture(1)   =   "frmConsultaPagos.frx":177E
      Tab(1).ControlEnabled=   0   'False
      Tab(1).Control(0)=   "fgPagosFINSOL"
      Tab(1).ControlCount=   1
      Begin MSFlexGridLib.MSFlexGrid fgPagosFINFIN 
         Height          =   5925
         Left            =   60
         TabIndex        =   8
         Top             =   240
         Width           =   9765
         _ExtentX        =   17224
         _ExtentY        =   10451
         _Version        =   393216
         Rows            =   0
         FixedRows       =   0
         FixedCols       =   0
         BackColor       =   8388608
         BackColorFixed  =   14737632
         BackColorBkg    =   14737632
         SelectionMode   =   1
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
      Begin MSFlexGridLib.MSFlexGrid fgPagosFINSOL 
         Height          =   5800
         Left            =   -74940
         TabIndex        =   23
         Top             =   360
         Width           =   9765
         _ExtentX        =   17224
         _ExtentY        =   10239
         _Version        =   393216
         Rows            =   0
         FixedRows       =   0
         FixedCols       =   0
         BackColor       =   8388608
         BackColorFixed  =   14737632
         BackColorBkg    =   14737632
         SelectionMode   =   1
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
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9945
      TabIndex        =   1
      Top             =   0
      Width           =   9945
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   360
         Picture         =   "frmConsultaPagos.frx":179A
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   24
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
         Left            =   9540
         TabIndex        =   4
         Top             =   120
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
         Left            =   8370
         TabIndex        =   3
         Top             =   210
         Width           =   1170
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Consulta de Pagos"
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
         TabIndex        =   2
         Top             =   60
         Width           =   4245
      End
   End
   Begin AdminCred.ctlBoton BtnCerrar 
      Height          =   270
      Left            =   8610
      TabIndex        =   0
      Top             =   10200
      Width           =   1260
      _ExtentX        =   2223
      _ExtentY        =   476
   End
   Begin MSComctlLib.ProgressBar pbarConsultar 
      Height          =   195
      Left            =   5130
      TabIndex        =   5
      Top             =   10620
      Width           =   1995
      _ExtentX        =   3519
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   6
      Top             =   10560
      Width           =   9945
      _ExtentX        =   17542
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de consulta de pagos "
            TextSave        =   "Módulo de consulta de pagos "
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
   Begin MSFlexGridLib.MSFlexGrid fgPagosOld 
      Height          =   1095
      Left            =   11010
      TabIndex        =   20
      Top             =   7290
      Width           =   1125
      _ExtentX        =   1984
      _ExtentY        =   1931
      _Version        =   393216
      Rows            =   0
      FixedRows       =   0
      FixedCols       =   0
      BackColor       =   0
      BackColorFixed  =   14737632
      BackColorBkg    =   16773360
      SelectionMode   =   1
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
   Begin MSFlexGridLib.MSFlexGrid fgPagosNoImp 
      Height          =   1290
      Left            =   11640
      TabIndex        =   21
      Top             =   2880
      Width           =   885
      _ExtentX        =   1561
      _ExtentY        =   2275
      _Version        =   393216
      BackColorFixed  =   14737632
      BackColorBkg    =   -2147483632
      SelectionMode   =   1
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
   Begin MSFlexGridLib.MSFlexGrid fgPagosNoIden 
      Height          =   960
      Left            =   11220
      TabIndex        =   22
      Top             =   4710
      Width           =   1185
      _ExtentX        =   2090
      _ExtentY        =   1693
      _Version        =   393216
      BackColorFixed  =   14737632
      BackColorBkg    =   -2147483632
      SelectionMode   =   1
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
   Begin VB.Label lbTotRecargos 
      Alignment       =   1  'Right Justify
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
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
      ForeColor       =   &H00FF7070&
      Height          =   300
      Left            =   7890
      TabIndex        =   19
      Top             =   9810
      Width           =   1995
   End
   Begin VB.Label lbTotIntereses 
      Alignment       =   1  'Right Justify
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
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
      ForeColor       =   &H00FF7070&
      Height          =   300
      Left            =   5640
      TabIndex        =   18
      Top             =   9810
      Width           =   1995
   End
   Begin VB.Label lbTotCapital 
      Alignment       =   1  'Right Justify
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
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
      ForeColor       =   &H00FF7070&
      Height          =   300
      Left            =   3390
      TabIndex        =   17
      Top             =   9810
      Width           =   1995
   End
   Begin VB.Label lbTotCantidad 
      Alignment       =   1  'Right Justify
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
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
      ForeColor       =   &H00FF7070&
      Height          =   300
      Left            =   1110
      TabIndex        =   16
      Top             =   9810
      Width           =   1995
   End
   Begin VB.Label lbTotNoPagos 
      Alignment       =   1  'Right Justify
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
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
      ForeColor       =   &H00FF7070&
      Height          =   300
      Left            =   60
      TabIndex        =   15
      Top             =   9810
      Width           =   795
   End
   Begin VB.Label Label37 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Recargos:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   195
      Left            =   7890
      TabIndex        =   14
      Top             =   9600
      Width           =   870
   End
   Begin VB.Label Label36 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Intereses:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   195
      Left            =   5640
      TabIndex        =   13
      Top             =   9600
      Width           =   885
   End
   Begin VB.Label Label35 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Capital:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   195
      Left            =   3390
      TabIndex        =   12
      Top             =   9600
      Width           =   675
   End
   Begin VB.Label Label34 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Cantidad:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   195
      Left            =   1110
      TabIndex        =   11
      Top             =   9600
      Width           =   840
   End
   Begin VB.Label Label33 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Pagos:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   195
      Left            =   60
      TabIndex        =   10
      Top             =   9600
      Width           =   585
   End
   Begin VB.Menu mnuOpciones 
      Caption         =   "&Opciones"
      Visible         =   0   'False
      Begin VB.Menu mnuDetalle 
         Caption         =   "&Ver detalle"
      End
   End
End
Attribute VB_Name = "frmConsultaPagos"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private bCerrarForm As Boolean
Private lNoPagos As Double, lCantidad As Double, lCapital As Double, lIntereses As Double, lRecargos As Double, lNoRegs As Long
Private bTotNoPagos As Double, bTotCantidad As Double, bTotCapital As Double, bTotIntereses As Double, bTotRecargos As Double
Private bTotalNoPagos As Double, bTotalCantidad As Double, bTotalCapital As Double, bTotalIntereses As Double, bTotalRecargos As Double
Private Const EMP_FINFIN = "< < < < < <          E M P F I N         > > > > >"
Private Const EMP_FINSOL = "< < < < < <          O T R O S           > > > > >"
Private Const ENC_IDEN = "I D E N T I F I C A D O S"
Private Const ENC_NO_IDEN = "G A R A N T I A S"
Private Const ENC_NO_IMP = "N O   I M P O R T A D O S"

Private Sub BtnCerrar_BotonClick()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    bCerrarForm = True
    Unload Me
    
    Screen.MousePointer = vbDefault
    
    Exit Sub
RutinaError:
    Screen.MousePointer = vbDefault
    MsgBox "Se ha generado el siguiente error:" & vbNewLine & "No.: " & CStr(Err.Number) & vbNewLine & "Descripción: " & Err.Description, vbOKOnly + vbCritical, "Error del sistema"
End Sub

Private Sub ctlFiltroConciliacion_ClickBuscar()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset, lTotalNoPagos As Long

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call BorrarFilasGrids
    Call ProcesarFINFIN
    'Call ProcesarFINSOL
    
    lbTotNoPagos.Caption = CStr(bTotalNoPagos)
    lbTotCantidad.Caption = Format(CStr(bTotalCantidad), "$###,###,###,###,###,##0.00")
    lbTotCapital.Caption = Format(CStr(bTotalCapital), "$###,###,###,###,###,##0.00")
    lbTotIntereses.Caption = Format(CStr(bTotalIntereses), "$###,###,###,###,###,##0.00")
    lbTotRecargos.Caption = Format(CStr(bTotalRecargos), "$###,###,###,###,###,##0.00")
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ProcesarFINFIN()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset, lTotalNoPagos As Long

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    'Call BorrarFilasGrids
    Call PonerEncabezado(ENC_IDEN, fgPagosFINFIN, &H8000&, &HE0E0E0)
    
    lNoRegs = 0
    lNoPagos = 0
    lCantidad = 0
    lCapital = 0
    lIntereses = 0
    lRecargos = 0
    bTotNoPagos = 0
    bTotCantidad = 0
    bTotCapital = 0
    bTotIntereses = 0
    bTotRecargos = 0
    DoEvents
    lbTotNoPagos.Caption = "0"
    DoEvents
    lbTotCantidad.Caption = "$0.00"
    DoEvents
    lbTotCapital.Caption = "$0.00"
    DoEvents
    lbTotIntereses.Caption = "$0.00"
    DoEvents
    lbTotRecargos.Caption = "$0.00"
    DoEvents
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select   count(*)       as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(cantidad)  as cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadocap) as capital," & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadoint) as intereses, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadorec) as recargos, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem          as cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb          as cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         frealdep       as frealdep " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     mp " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    frealdep = '" & Format(ctlFiltroConciliacion.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      estatus  <> 'E' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgcb    is not null " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgem    = 'EMPFIN' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         frealdep " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb " & vbNewLine
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Call LlenarGrid(fgPagosFINFIN, oRstConsultar, 3)
            Call InsertarSeparador(fgPagosFINFIN, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(fgPagosFINFIN, oRstConsultar, 0)
                oRstConsultar.IrAlRegSiguiente
            Wend
            
            Call InsertarTot(fgPagosFINFIN, 0, &H8000&)
            
            bTotNoPagos = lNoPagos
            bTotCantidad = lCantidad
            bTotCapital = lCapital
            bTotIntereses = lIntereses
            bTotRecargos = lRecargos
            
            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    Call PonerEncabezado(ENC_NO_IDEN, fgPagosFINFIN, &HFF7070, &HE0E0E0)
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select        cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "count(*)      as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "sum(cantidad) as cantidad " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from          PAG_GAR_SIM " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where         FPAGO = '" & Format(ctlFiltroConciliacion.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgem = 'EMPFIN' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           estatus = 'RE' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb "

    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Call LlenarGrid(fgPagosFINFIN, oRstConsultar, 3)
            Call InsertarSeparador(fgPagosFINFIN, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(fgPagosFINFIN, oRstConsultar, 1)
                oRstConsultar.IrAlRegSiguiente
            Wend

            Call InsertarTot(fgPagosFINFIN, 1, &HFF8080)
            
            bTotNoPagos = bTotNoPagos + lNoPagos
            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    Call PonerEncabezado(ENC_NO_IMP, fgPagosFINFIN, &HFF, &HE0E0E0)
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select        cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "count(*)      as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "sum(cantidad) as cantidad " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from          pdi " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where         fdeposito = '" & Format(ctlFiltroConciliacion.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgem = 'EMPFIN' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           estatus = 'RE' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb "
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Call LlenarGrid(fgPagosFINFIN, oRstConsultar, 3)
            Call InsertarSeparador(fgPagosFINFIN, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(fgPagosFINFIN, oRstConsultar, 2)
                oRstConsultar.IrAlRegSiguiente
            Wend

            Call InsertarTot(fgPagosFINFIN, 2, &HFF)
            
            bTotNoPagos = bTotNoPagos + lNoPagos
            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select

    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    Call InsertarSeparador(fgPagosFINFIN, vbBlack)
    Call InsertarSeparador(fgPagosFINFIN, vbBlack)
    Call InsertarTot(fgPagosFINFIN, 3, vbBlack)
    
    bTotalNoPagos = bTotNoPagos
    bTotalCantidad = bTotCantidad
    bTotalCapital = bTotCapital
    bTotalIntereses = bTotIntereses
    bTotalRecargos = bTotRecargos
    
    Screen.MousePointer = vbDefault
'    If (bTotNoPagos = 0) Then MsgBox "No se encontró información para la fecha proporcionada...", vbInformation + vbOKOnly, TITULO_MENSAJE
    sbBarraEstado.Panels(1).Text = "Módulo de consulta de pagos"
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ProcesarFINSOL()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset, lTotalNoPagos As Long

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    'Call BorrarFilasGrids
    Call PonerEncabezado(ENC_IDEN, fgPagosFINSOL, &H8000&, &HE0E0E0)
    
    lNoRegs = 0
    lNoPagos = 0
    lCantidad = 0
    lCapital = 0
    lIntereses = 0
    lRecargos = 0
    bTotNoPagos = 0
    bTotCantidad = 0
    bTotCapital = 0
    bTotIntereses = 0
    bTotRecargos = 0
    DoEvents
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select   count(*)       as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(cantidad)  as cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadocap) as capital," & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadoint) as intereses, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadorec) as recargos, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem          as cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb          as cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         frealdep       as frealdep " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     mp " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    frealdep = '" & Format(ctlFiltroConciliacion.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgcb    is not null " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgem    = 'FINSOL' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         frealdep " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb " & vbNewLine
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 3)
            Call InsertarSeparador(fgPagosFINSOL, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 0)
                oRstConsultar.IrAlRegSiguiente
            Wend
            
            Call InsertarTot(fgPagosFINSOL, 0, &H8000&)
            
            bTotNoPagos = lNoPagos
            bTotCantidad = lCantidad
            bTotCapital = lCapital
            bTotIntereses = lIntereses
            bTotRecargos = lRecargos
            
            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    Call PonerEncabezado(ENC_NO_IDEN, fgPagosFINSOL, &HFF7070, &HE0E0E0)
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select        cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "count(*)      as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "sum(cantidad) as cantidad " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from          pdi " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where         fdeposito = '" & Format(ctlFiltroConciliacion.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgem = 'FINSOL' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb "

    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 3)
            Call InsertarSeparador(fgPagosFINSOL, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 1)
                oRstConsultar.IrAlRegSiguiente
            Wend

            Call InsertarTot(fgPagosFINSOL, 1, &HFF8080)
            
            bTotNoPagos = bTotNoPagos + lNoPagos
            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    Call PonerEncabezado(ENC_NO_IMP, fgPagosFINSOL, &HFF, &HE0E0E0)
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select   cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         ctabancaria as cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         count(*)    as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(Monto)  As cantidad " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     res_impor " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    fechapago = '" & ctlFiltroConciliacion.FechaPago & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      validacion in (2, 4) " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgem = 'FINSOL' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         ctabancaria " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         ctabancaria "

    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 3)
            Call InsertarSeparador(fgPagosFINSOL, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 2)
                oRstConsultar.IrAlRegSiguiente
            Wend

            Call InsertarTot(fgPagosFINSOL, 2, &HFF)
            
            bTotNoPagos = bTotNoPagos + lNoPagos
            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select

    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    Call InsertarSeparador(fgPagosFINSOL, vbBlack)
    Call InsertarSeparador(fgPagosFINSOL, vbBlack)
    Call InsertarTot(fgPagosFINSOL, 3, vbBlack)
    
    If bTotNoPagos > 0 Then bTotalNoPagos = bTotalNoPagos + bTotNoPagos
    If bTotCantidad > 0 Then bTotalCantidad = bTotalCantidad + bTotCantidad
    If bTotCapital > 0 Then bTotalCapital = bTotalCapital + bTotCapital
    If bTotIntereses > 0 Then bTotalIntereses = bTotalIntereses + bTotIntereses
    If bTotRecargos > 0 Then bTotalRecargos = bTotalRecargos + bTotRecargos
    
    Screen.MousePointer = vbDefault
'    If (bTotNoPagos = 0) Then MsgBox "No se encontró información para la fecha proporcionada...", vbInformation + vbOKOnly, TITULO_MENSAJE
    sbBarraEstado.Panels(1).Text = "Módulo de consulta de pagos"
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub InsertarTot(ByVal poGrid As MSFlexGrid, ByVal piOpcion As Integer, poColorFrente As Variant)
    Dim vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With poGrid
        .Rows = .Rows + 1
        .Row = .Rows - 1
        .RowHeight(.Row) = 40
        
        If (.Row Mod 2 = 1) Then
            vColorFrente = vbBlack
            vColorFondo = &HFFF5F5
        Else
            vColorFrente = vbBlack
            vColorFondo = vbWhite
        End If
        
        .Col = 0
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vbBlack
        .CellBackColor = &HE0E0E0
        '.Text = CStr(lNoRegs) & " "
        
        .Col = 1
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = ""
        
        .Col = 2
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = ""
        
        .Col = 3
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .CellFontBold = True
        .Text = "----------"
        
        .Col = 4
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .CellFontBold = True
        .Text = "----------------------"
        
        .Col = 5
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .CellFontBold = True
        .Text = "----------------------"
        
        .Col = 6
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .CellFontBold = True
        .Text = "----------------------"
        
        .Col = 7
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .CellFontBold = True
        .Text = "----------------"
        
        .Rows = .Rows + 1
        .Row = .Rows - 1
        
        If (.Row Mod 2 = 1) Then
            vColorFrente = poColorFrente
            vColorFondo = &HFFF5F5
        Else
            vColorFrente = poColorFrente
            vColorFondo = vbWhite
        End If
        
        .Col = 0
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vbBlack
        .CellBackColor = &HE0E0E0
        '.Text = CStr(lNoRegs) & " "
        
        .Col = 1
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = ""
        
        .Col = 2
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vbBlack
        .CellBackColor = vColorFondo
        .CellFontBold = True
        .CellFontSize = 7
        If (piOpcion = 3) Then
            .Text = "T O T A L E S"
        Else
            .Text = ""
        End If
        
        Select Case piOpcion
            Case 0
                .Col = 3
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = CStr(lNoPagos)
            
                .Col = 4
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(lCantidad, "$###,###,###,###,##0.00")
        
                .Col = 5
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(lCapital, "$###,###,###,###,##0.00")
                
                .Col = 6
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(lIntereses, "$###,###,###,###,##0.00")
                
                .Col = 7
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(lRecargos, "$###,###,###,###,##0.00")
            Case 1, 2
                .Col = 3
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = CStr(lNoPagos)
            
                .Col = 4
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(lCantidad, "$###,###,###,###,##0.00")
            
                .Col = 5
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = "N/A"
                
                .Col = 6
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = "N/A"
                
                .Col = 7
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = "N/A"
            Case 3
                .Col = 3
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = CStr(bTotNoPagos)
            
                .Col = 4
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(bTotCantidad, "$###,###,###,###,##0.00")
        
                .Col = 5
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(bTotCapital, "$###,###,###,###,##0.00")
                
                .Col = 6
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(bTotIntereses, "$###,###,###,###,##0.00")
                
                .Col = 7
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = poColorFrente
                .CellBackColor = vColorFondo
                .CellFontBold = True
                .CellFontSize = 8
                .Text = Format(bTotRecargos, "$###,###,###,###,##0.00")
        End Select
        
        .Rows = .Rows + 1
        .Row = .Rows - 1
        
        If (.Row Mod 2 = 1) Then
            vColorFrente = poColorFrente
            vColorFondo = &HFFF5F5
        Else
            vColorFrente = poColorFrente
            vColorFondo = vbWhite
        End If
        
        .RowHeight(.Row) = 40
        .Col = 0
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 1
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 2
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 3
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 4
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 5
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 6
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 7
        .CellBackColor = &HC0C0C0
        .Text = ""
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub InsertarSeparador(ByVal poGrid As MSFlexGrid, ByVal poColorFrente As Variant)
    Dim vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With poGrid
        .Rows = .Rows + 1
        .Row = .Rows - 1
        
        If (.Row Mod 2 = 1) Then
            vColorFrente = poColorFrente
            vColorFondo = &HFFF5F5
        Else
            vColorFrente = poColorFrente
            vColorFondo = vbWhite
        End If
        
        .RowHeight(.Row) = 40
        .Col = 0
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 1
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 2
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 3
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 4
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 5
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 6
        .CellBackColor = &HC0C0C0
        .Text = ""
        
        .Col = 7
        .CellBackColor = &HC0C0C0
        .Text = ""
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ctlFiltroConciliacion_ClickCodigo()
    ctlFiltroConciliacion.OptCodigo = False
End Sub

Private Sub ctlFiltroConciliacion_ClickCtaBancaria()
    ctlFiltroConciliacion.OptCtaBancaria = False
End Sub

Private Sub ctlFiltroConciliacion_ClickEmpresa()
    ctlFiltroConciliacion.OptEmpresa = False
End Sub

Private Sub ctlFiltroConciliacion_ClickFechaPago()
    ctlFiltroConciliacion.OptFechaPago = True
End Sub

Private Sub ctlFiltroConciliacion_ClickNombre()
    ctlFiltroConciliacion.OptNombre = False
End Sub

Private Sub ctlFiltroConciliacion_ClickTipoCliente()
    ctlFiltroConciliacion.OptTipoCliente = False
End Sub

Private Sub fgPagosFINFIN_DblClick()
    On Error GoTo RutinaError

    If (Trim(fgPagosFINFIN.TextMatrix(fgPagosFINFIN.Row, 1)) <> "" And fgPagosFINFIN.Row <> 0 And IsNumeric(fgPagosFINFIN.TextMatrix(fgPagosFINFIN.Row, 0))) Then
        With cCtaBancaria
            .Empresa = fgPagosFINFIN.TextMatrix(fgPagosFINFIN.Row, 1)
            .FechaPago = ctlFiltroConciliacion.FechaPago
            .NoCta = fgPagosFINFIN.TextMatrix(fgPagosFINFIN.Row, 2)
            Select Case fgPagosFINFIN.TextMatrix(fgPagosFINFIN.Row - (Val(fgPagosFINFIN.TextMatrix(fgPagosFINFIN.Row, 0)) + 1), 0)
                Case ENC_IDEN
                    .TipoPagos = "1"
                Case ENC_NO_IDEN
                    .TipoPagos = "2"
                Case ENC_NO_IMP
                    .TipoPagos = "3"
            End Select
        End With
        frmDetallePagos.Show 1, Me
    End If

    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub fgPagosFINSOL_DblClick()
    On Error GoTo RutinaError

    If (Trim(fgPagosFINSOL.TextMatrix(fgPagosFINSOL.Row, 1)) <> "" And fgPagosFINSOL.Row <> 0 And IsNumeric(fgPagosFINSOL.TextMatrix(fgPagosFINSOL.Row, 0))) Then
        With cCtaBancaria
            .Empresa = fgPagosFINSOL.TextMatrix(fgPagosFINSOL.Row, 1)
            .FechaPago = ctlFiltroConciliacion.FechaPago
            .NoCta = fgPagosFINSOL.TextMatrix(fgPagosFINSOL.Row, 2)
            Select Case fgPagosFINSOL.TextMatrix(fgPagosFINSOL.Row - (Val(fgPagosFINSOL.TextMatrix(fgPagosFINSOL.Row, 0)) + 1), 0)
                Case ENC_IDEN
                    .TipoPagos = "1"
                Case ENC_NO_IDEN
                    .TipoPagos = "2"
                Case ENC_NO_IMP
                    .TipoPagos = "3"
            End Select
        End With
        frmDetallePagos.Show 1, Me
    End If

    Exit Sub
RutinaError:
    MensajeError Err
End Sub


'''''Private Sub fgPagosIden_DblClick()
'''''    On Error GoTo RutinaError
''''''    Screen.MousePointer = vbHourglass
'''''
'''''    If (fgPagosIden.Rows > 1 And Trim(fgPagosIden.TextMatrix(fgPagosIden.Row, 1)) <> "" And fgPagosIden.Row <> 0) Then
'''''        frmDetallePagos.Show 1, Me
'''''    End If
'''''
''''''    Screen.MousePointer = vbDefault
'''''    Exit Sub
'''''RutinaError:
'''''    MensajeError Err
'''''End Sub

'''Private Sub fgPagosIden_MouseUp(Button As Integer, Shift As Integer, x As Single, y As Single)
'''    On Error GoTo RutinaError
''''    Screen.MousePointer = vbHourglass
'''
'''    If Button = 2 And fgPagosIden.Rows > 1 Then
'''        PopupMenu mnuOpciones
'''    End If
'''
''''    Screen.MousePointer = vbDefault
'''    Exit Sub
'''RutinaError:
'''    MensajeError Err
'''End Sub

Private Sub fgPagosNoIden_DblClick()
    On Error GoTo RutinaError
'    Screen.MousePointer = vbHourglass

    If (fgPagosNoIden.Rows > 1 And Trim(fgPagosNoIden.TextMatrix(fgPagosNoIden.Row, 1)) <> "" And fgPagosNoIden.Row <> 0) Then
        frmDetallePagos.Show 1, Me
    End If

'    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

'''Private Sub fgPagosNoIden_MouseUp(Button As Integer, Shift As Integer, x As Single, y As Single)
'''    On Error GoTo RutinaError
''''    Screen.MousePointer = vbHourglass
'''
'''    If Button = 2 And fgPagosNoIden.Rows > 1 Then
'''        PopupMenu mnuOpciones
'''    End If
'''
''''    Screen.MousePointer = vbDefault
'''    Exit Sub
'''RutinaError:
'''    MensajeError Err
'''End Sub

Private Sub fgPagosNoImp_DblClick()
    On Error GoTo RutinaError
'    Screen.MousePointer = vbHourglass

    If (fgPagosNoImp.Rows > 1 And Trim(fgPagosNoImp.TextMatrix(fgPagosNoImp.Row, 1)) <> "" And fgPagosNoImp.Row <> 0) Then
        frmDetallePagos.Show 1, Me
    End If

'    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

'''Private Sub fgPagosNoImp_MouseUp(Button As Integer, Shift As Integer, x As Single, y As Single)
'''    On Error GoTo RutinaError
''''    Screen.MousePointer = vbHourglass
'''
'''    If Button = 2 And fgPagosNoImp.Rows > 1 Then
'''        PopupMenu mnuOpciones
'''    End If
'''
''''    Screen.MousePointer = vbDefault
'''    Exit Sub
'''RutinaError:
'''    MensajeError Err
'''End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    bCerrarForm = False
    SSTPagos.Tab = 0
    BtnCerrar.Texto = "&Cerrar"
    ctlFiltroConciliacion.OptFechaPago = True
    ctlFiltroConciliacion.QuitarFiltro = False
    ctlFiltroConciliacion.OptEmpresa = False
    ctlFiltroConciliacion.OptCtaBancaria = False
    ctlFiltroConciliacion.OptNombre = False
    ctlFiltroConciliacion.OptTipoCliente = False
    ctlFiltroConciliacion.OptCodigo = False

    Call InicializaGrid(EMP_FINFIN, fgPagosFINFIN)
    Call InicializaGrid(EMP_FINSOL, fgPagosFINSOL)
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub
Public Sub InicializaGrid(ByVal psEmpresa As String, ByVal poGrid As MSFlexGrid)
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With poGrid
        .Cols = 8
        .Rows = .Rows + 1
        .Row = .Rows - 1
        .RowHeight(0) = 270
        .Col = 0
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(0) = 500
        
        .Col = 1
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(1) = 800
        
        .Col = 2
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(2) = 1200
        
        .Col = 3
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(3) = 850
        
        .Col = 4
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(4) = 1600
        
        .Col = 5
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(5) = 1600
        
        .Col = 6
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(6) = 1600
        
        .Col = 7
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = &H800000
        .CellForeColor = vbWhite
        .CellFontSize = 8
        .Text = psEmpresa
        .ColWidth(7) = 1200
        
        poGrid.MergeCells = flexMergeFree
        poGrid.MergeRow(0) = True
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub PonerEncabezado(ByVal psEncabezado As String, ByVal poGrid As MSFlexGrid, poColorFrente As Variant, poColorFondo As Variant)
    Dim lRenglon As Long, vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    vColorFrente = poColorFrente
    vColorFondo = poColorFondo
    
    With poGrid
        .Cols = 8
        .Rows = .Rows + 1
        .Row = .Rows - 1
        .RowHeight(0) = 270
        .Col = 0
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(0) = 500
        
        .Col = 1
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(1) = 800
        
        .Col = 2
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(2) = 1200
        
        .Col = 3
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(3) = 850
        
        .Col = 4
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(4) = 1600
        
        .Col = 5
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(5) = 1600
        
        .Col = 6
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(6) = 1600
        
        .Col = 7
        .CellAlignment = flexAlignCenterCenter
        .CellFontBold = True
        .CellBackColor = poColorFondo
        .CellForeColor = vColorFrente
        '.CellTextStyle = flexTextInset
        .Text = psEncabezado
        .ColWidth(7) = 1200
        
        'Ahora colocamos los encabezados de las Columnas
        .Rows = .Rows + 1
        .Row = .Rows - 1
        .RowHeight(0) = 270
        .Col = 0
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "No."
        .ColWidth(0) = 500
        
        .Col = 1
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "Empresa"
        .ColWidth(1) = 800
        
        .Col = 2
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "Cta. Bancaria"
        .ColWidth(2) = 1200
        
        .Col = 3
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "No. Pagos"
        .ColWidth(3) = 850
        
        .Col = 4
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "Cantidad"
        .ColWidth(4) = 1600
        
        .Col = 5
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "Capital"
        .ColWidth(5) = 1600
        
        .Col = 6
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "Intereses"
        .ColWidth(6) = 1600
        
        .Col = 7
        .CellAlignment = flexAlignCenterCenter
        '.CellFontBold = True
        .CellBackColor = &HE0E0E0
        '.CellTextStyle = flexTextInset
        .Text = "Recargos"
        .ColWidth(7) = 1200
        
        poGrid.MergeCells = flexMergeFree
        poGrid.MergeRow(poGrid.Row - 1) = True
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

''''''''''''Public Sub InicializaGridsFINFIN()
''''''''''''    On Error GoTo RutinaError
''''''''''''    Screen.MousePointer = vbHourglass
''''''''''''
''''''''''''    With fgPagosIden
''''''''''''        .Rows = 1
''''''''''''        .Cols = 8
''''''''''''        .Row = .Rows - 1
''''''''''''        .RowHeight(0) = 270
''''''''''''        .RowHeight(0) = 270
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(4) = 1600
''''''''''''
''''''''''''        .Col = 5
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(5) = 1600
''''''''''''
''''''''''''        .Col = 6
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(6) = 1600
''''''''''''
''''''''''''        .Col = 7
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  F  I  N   >>>"
''''''''''''        .ColWidth(7) = 1200
''''''''''''
''''''''''''        'Ahora colocamos los encabezados de las Columnas
''''''''''''        .Rows = .Rows + 1
''''''''''''        .Row = .Rows - 1
''''''''''''        .RowHeight(0) = 270
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "No."
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Empresa"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Cta. Bancaria"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "No. Pagos"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Cantidad"
''''''''''''        .ColWidth(4) = 1600
''''''''''''
''''''''''''        .Col = 5
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Capital"
''''''''''''        .ColWidth(5) = 1600
''''''''''''
''''''''''''        .Col = 6
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Intereses"
''''''''''''        .ColWidth(6) = 1600
''''''''''''
''''''''''''        .Col = 7
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Recargos"
''''''''''''        .ColWidth(7) = 1200
''''''''''''
''''''''''''        fgPagosIden.MergeCells = flexMergeFree
''''''''''''        fgPagosIden.MergeRow(0) = True
''''''''''''    End With
''''''''''''
''''''''''''    With fgPagosNoIden
''''''''''''        .Rows = 1
''''''''''''        .Cols = 5
''''''''''''        .Row = 0
''''''''''''        .RowHeight(0) = 270
''''''''''''
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No."
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Empresa"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cta. Bancaria"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No. Pagos"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cantidad"
''''''''''''        .ColWidth(4) = 1600
''''''''''''    End With
''''''''''''
''''''''''''    With fgPagosNoImp
''''''''''''        .Rows = 1
''''''''''''        .Cols = 5
''''''''''''        .Row = 0
''''''''''''        .RowHeight(0) = 270
''''''''''''
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No."
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Empresa"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cta. Bancaria"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No. Pagos"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cantidad"
''''''''''''        .ColWidth(4) = 1600
''''''''''''    End With
''''''''''''
''''''''''''    Screen.MousePointer = vbDefault
''''''''''''    Exit Sub
''''''''''''RutinaError:
''''''''''''    MensajeError Err
''''''''''''End Sub

''''''''''''Public Sub InicializaGridsFINSOL()
''''''''''''    On Error GoTo RutinaError
''''''''''''    Screen.MousePointer = vbHourglass
''''''''''''
''''''''''''    With fgPagosIden
''''''''''''        .Rows = .Rows + 2
''''''''''''        .Cols = 8
''''''''''''        .Row = .Rows - 1
''''''''''''        .RowHeight(0) = 270
''''''''''''        .RowHeight(0) = 270
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(4) = 1600
''''''''''''
''''''''''''        .Col = 5
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(5) = 1600
''''''''''''
''''''''''''        .Col = 6
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(6) = 1600
''''''''''''
''''''''''''        .Col = 7
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &H80C0FF
''''''''''''        .Text = "<<<   F  I  N  S  O  L   >>>"
''''''''''''        .ColWidth(7) = 1200
''''''''''''
''''''''''''        'Ahora colocamos los encabezados de las Columnas
''''''''''''        .Rows = .Rows + 1
''''''''''''        .Row = .Rows - 1
''''''''''''        .RowHeight(0) = 270
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "No."
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Empresa"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Cta. Bancaria"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "No. Pagos"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Cantidad"
''''''''''''        .ColWidth(4) = 1600
''''''''''''
''''''''''''        .Col = 5
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Capital"
''''''''''''        .ColWidth(5) = 1600
''''''''''''
''''''''''''        .Col = 6
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Intereses"
''''''''''''        .ColWidth(6) = 1600
''''''''''''
''''''''''''        .Col = 7
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .CellBackColor = &HE0E0E0
''''''''''''        .Text = "Recargos"
''''''''''''        .ColWidth(7) = 1200
''''''''''''
''''''''''''        fgPagosIden.MergeCells = flexMergeFree
''''''''''''        fgPagosIden.MergeRow(0) = True
''''''''''''    End With
''''''''''''
''''''''''''    With fgPagosNoIden
''''''''''''        .Rows = 1
''''''''''''        .Cols = 5
''''''''''''        .Row = 0
''''''''''''        .RowHeight(0) = 270
''''''''''''
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No."
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Empresa"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cta. Bancaria"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No. Pagos"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cantidad"
''''''''''''        .ColWidth(4) = 1600
''''''''''''    End With
''''''''''''
''''''''''''    With fgPagosNoImp
''''''''''''        .Rows = 1
''''''''''''        .Cols = 5
''''''''''''        .Row = 0
''''''''''''        .RowHeight(0) = 270
''''''''''''
''''''''''''        .Col = 0
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No."
''''''''''''        .ColWidth(0) = 500
''''''''''''
''''''''''''        .Col = 1
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Empresa"
''''''''''''        .ColWidth(1) = 800
''''''''''''
''''''''''''        .Col = 2
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cta. Bancaria"
''''''''''''        .ColWidth(2) = 1200
''''''''''''
''''''''''''        .Col = 3
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "No. Pagos"
''''''''''''        .ColWidth(3) = 850
''''''''''''
''''''''''''        .Col = 4
''''''''''''        .CellAlignment = flexAlignCenterCenter
''''''''''''        .CellFontBold = True
''''''''''''        .Text = "Cantidad"
''''''''''''        .ColWidth(4) = 1600
''''''''''''    End With
''''''''''''
''''''''''''    Screen.MousePointer = vbDefault
''''''''''''    Exit Sub
''''''''''''RutinaError:
''''''''''''    MensajeError Err
''''''''''''End Sub

Public Sub LlenarGrid(ByVal poGrid As MSFlexGrid, ByVal poRst As clsoAdoRecordset, ByVal piOpcion As Integer)
    Dim vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With poGrid
            .Rows = .Rows + 1
            .Row = .Rows - 1
            
            If (.Row Mod 2 = 1) Then
                vColorFrente = vbBlack
                vColorFondo = &HFFF5F5
                'vColorFondo = vbWhite
            Else
                vColorFrente = vbBlack
                vColorFondo = vbWhite
            End If
            
            Select Case piOpcion
                Case 0
                    .Col = 0
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vbBlack
                    .CellBackColor = &HE0E0E0
                    '.Text = CStr(.Row - 2) & " "
                    .Text = CStr(lNoRegs) & " "
                    
                    .Col = 1
                    .CellAlignment = flexAlignCenterCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
                    
                    .Col = 2
                    .CellAlignment = flexAlignCenterCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
                    
                    .Col = 3
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), "0", poRst.ObjSetRegistros.Fields("no_regs").Value)
                    lNoPagos = lNoPagos + IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), 0, poRst.ObjSetRegistros.Fields("no_regs").Value)
                    
                    .Col = 4
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
                    lCantidad = lCantidad + IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), 0, poRst.ObjSetRegistros.Fields("cantidad").Value)
                
                    .Col = 5
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("capital").Value), "$0.00", poRst.ObjSetRegistros.Fields("capital").Value), "$###,###,###,###,###,##0.00")
                    lCapital = lCapital + IIf(IsNull(poRst.ObjSetRegistros.Fields("capital").Value), 0, poRst.ObjSetRegistros.Fields("capital").Value)
                    
                    .Col = 6
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("intereses").Value), "$0.00", poRst.ObjSetRegistros.Fields("intereses").Value), "$###,###,###,###,###,##0.00")
                    lIntereses = lIntereses + IIf(IsNull(poRst.ObjSetRegistros.Fields("intereses").Value), 0, poRst.ObjSetRegistros.Fields("intereses").Value)
                    
                    .Col = 7
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("recargos").Value), "$0.00", poRst.ObjSetRegistros.Fields("recargos").Value), "$###,###,###,###,###,##0.00")
                    lRecargos = lRecargos + IIf(IsNull(poRst.ObjSetRegistros.Fields("recargos").Value), 0, poRst.ObjSetRegistros.Fields("recargos").Value)
                Case 1, 2
                    .Col = 0
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vbBlack
                    .CellBackColor = &HE0E0E0
                    '.Text = CStr(.Row - 2) & " "
                    .Text = CStr(lNoRegs) & " "
                    
                    .Col = 1
                    .CellAlignment = flexAlignCenterCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
                    
                    .Col = 2
                    .CellAlignment = flexAlignCenterCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
                    
                    .Col = 3
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), "0", poRst.ObjSetRegistros.Fields("no_regs").Value)
                    lNoPagos = lNoPagos + IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), 0, poRst.ObjSetRegistros.Fields("no_regs").Value)
                    
                    .Col = 4
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
                    lCantidad = lCantidad + IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), 0, poRst.ObjSetRegistros.Fields("cantidad").Value)
                
                    .Col = 5
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "N/A"
                    
                    .Col = 6
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "N/A"
                    
                    .Col = 7
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "N/A"
                Case 3
                    .Col = 0
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vbBlack
                    .CellBackColor = &HE0E0E0
                    .Text = "1"
                    
                    .Col = 1
                    .CellAlignment = flexAlignCenterCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
                    
                    .Col = 2
                    .CellAlignment = flexAlignCenterCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
                    
                    .Col = 3
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
                    
                    .Col = 4
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
                
                    .Col = 5
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
                    
                    .Col = 6
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
                    
                    .Col = 7
                    .CellAlignment = flexAlignRightCenter
                    .CellForeColor = vColorFrente
                    .CellBackColor = vColorFondo
                    .Text = "-"
            End Select
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

'''''''''''Public Sub LlenarGridNoIden(ByVal poRst As clsoAdoRecordset)
'''''''''''    Dim vColorFrente As Variant, vColorFondo As Variant
'''''''''''
'''''''''''    On Error GoTo RutinaError
'''''''''''    Screen.MousePointer = vbHourglass
'''''''''''
'''''''''''    With fgPagosNoIden
'''''''''''            .Rows = .Rows + 1
'''''''''''            .Row = .Rows - 1
'''''''''''
'''''''''''            If (.Row Mod 2 = 1) Then
'''''''''''                vColorFrente = vbBlack
'''''''''''                vColorFondo = &HFFF5F5
'''''''''''            Else
'''''''''''                vColorFrente = vbBlack
'''''''''''                vColorFondo = vbWhite
'''''''''''            End If
'''''''''''
'''''''''''            .Col = 0
'''''''''''            .CellAlignment = flexAlignRightCenter
'''''''''''            .CellForeColor = vbBlack
'''''''''''            .CellBackColor = vbBlack
'''''''''''            .Text = CStr(.Row) & " "
'''''''''''
'''''''''''            .Col = 1
'''''''''''            .CellAlignment = flexAlignCenterCenter
'''''''''''            .CellForeColor = vColorFrente
'''''''''''            .CellBackColor = vColorFondo
'''''''''''            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
'''''''''''
'''''''''''            .Col = 2
'''''''''''            .CellAlignment = flexAlignCenterCenter
'''''''''''            .CellForeColor = vColorFrente
'''''''''''            .CellBackColor = vColorFondo
'''''''''''            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
'''''''''''
'''''''''''            .Col = 3
'''''''''''            .CellAlignment = flexAlignRightCenter
'''''''''''            .CellForeColor = vColorFrente
'''''''''''            .CellBackColor = vColorFondo
'''''''''''            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), "0", poRst.ObjSetRegistros.Fields("no_regs").Value)
'''''''''''            lNoPagos = lNoPagos + IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), 0, poRst.ObjSetRegistros.Fields("no_regs").Value)
'''''''''''
'''''''''''            .Col = 4
'''''''''''            .CellAlignment = flexAlignRightCenter
'''''''''''            .CellForeColor = vColorFrente
'''''''''''            .CellBackColor = vColorFondo
'''''''''''            .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
'''''''''''            lCantidad = lCantidad + IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), 0, poRst.ObjSetRegistros.Fields("cantidad").Value)
'''''''''''    End With
'''''''''''
'''''''''''    Screen.MousePointer = vbDefault
'''''''''''    Exit Sub
'''''''''''RutinaError:
'''''''''''    MensajeError Err
'''''''''''End Sub

'''''''''''''Public Sub LlenarGridNoImp(ByVal poRst As clsoAdoRecordset)
'''''''''''''    Dim vColorFrente As Variant, vColorFondo As Variant
'''''''''''''
'''''''''''''    On Error GoTo RutinaError
'''''''''''''    Screen.MousePointer = vbHourglass
'''''''''''''
'''''''''''''    With fgPagosNoImp
'''''''''''''            .Rows = .Rows + 1
'''''''''''''            .Row = .Rows - 1
'''''''''''''
'''''''''''''            If (.Row Mod 2 = 1) Then
'''''''''''''                vColorFrente = vbBlack
'''''''''''''                vColorFondo = &HFFF5F5
'''''''''''''            Else
'''''''''''''                vColorFrente = vbBlack
'''''''''''''                vColorFondo = vbWhite
'''''''''''''            End If
'''''''''''''
'''''''''''''            .Col = 0
'''''''''''''            .CellAlignment = flexAlignRightCenter
'''''''''''''            .CellForeColor = vbBlack
'''''''''''''            .CellBackColor = vbBlack
'''''''''''''            .Text = CStr(.Row) & " "
'''''''''''''
'''''''''''''            .Col = 1
'''''''''''''            .CellAlignment = flexAlignCenterCenter
'''''''''''''            .CellForeColor = vColorFrente
'''''''''''''            .CellBackColor = vColorFondo
'''''''''''''            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
'''''''''''''
'''''''''''''            .Col = 2
'''''''''''''            .CellAlignment = flexAlignCenterCenter
'''''''''''''            .CellForeColor = vColorFrente
'''''''''''''            .CellBackColor = vColorFondo
'''''''''''''            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
'''''''''''''
'''''''''''''            .Col = 3
'''''''''''''            .CellAlignment = flexAlignRightCenter
'''''''''''''            .CellForeColor = vColorFrente
'''''''''''''            .CellBackColor = vColorFondo
'''''''''''''            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), "0", poRst.ObjSetRegistros.Fields("no_regs").Value)
'''''''''''''            lNoPagos = lNoPagos + IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), 0, poRst.ObjSetRegistros.Fields("no_regs").Value)
'''''''''''''
'''''''''''''            .Col = 4
'''''''''''''            .CellAlignment = flexAlignRightCenter
'''''''''''''            .CellForeColor = vColorFrente
'''''''''''''            .CellBackColor = vColorFondo
'''''''''''''            .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
'''''''''''''            lCantidad = lCantidad + IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), 0, poRst.ObjSetRegistros.Fields("cantidad").Value)
'''''''''''''    End With
'''''''''''''
'''''''''''''    Screen.MousePointer = vbDefault
'''''''''''''    Exit Sub
'''''''''''''RutinaError:
'''''''''''''    MensajeError Err
'''''''''''''End Sub

Private Sub BorrarFilasGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    fgPagosFINFIN.Rows = 1
    fgPagosFINFIN.Refresh
    fgPagosFINSOL.Rows = 1
    fgPagosFINSOL.Refresh
    
    Screen.MousePointer = vbDefault
    
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    pbarConsultar.Width = sbBarraEstado.Panels(2).Width - 40
    pbarConsultar.Top = sbBarraEstado.Top + 60
    pbarConsultar.Left = sbBarraEstado.Panels(1).Width + 80
    pbarConsultar.Height = sbBarraEstado.Height - 100
    
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

Private Sub mnuDetalle_Click()
    On Error GoTo RutinaError
'    Screen.MousePointer = vbHourglass
    
    Select Case SSTPagos.Tab
        Case 0      'Identificados
            frmDetallePagos.Show 1, Me
        Case 1      'No Identificados
            frmDetallePagos.Show 1, Me
        Case 2      'No Importados
            frmDetallePagos.Show 1, Me
    End Select

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub
