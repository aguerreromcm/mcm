VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmBitacora 
   BackColor       =   &H00FFFFFF&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Bitácora de Importación y Conciliación de Pagos"
   ClientHeight    =   9120
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   12150
   Icon            =   "frmBitacora.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   9120
   ScaleWidth      =   12150
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   12150
      TabIndex        =   5
      Top             =   0
      Width           =   12150
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   360
         Picture         =   "frmBitacora.frx":0442
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   21
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
         Left            =   11760
         TabIndex        =   8
         Top             =   90
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
         Left            =   10590
         TabIndex        =   7
         Top             =   180
         Width           =   1170
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Bitácora de Importación y Conciliación de Pagos"
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
         Left            =   2160
         TabIndex        =   6
         Top             =   60
         Width           =   6975
      End
   End
   Begin MSFlexGridLib.MSFlexGrid fgPagos 
      Height          =   6675
      Left            =   60
      TabIndex        =   4
      Top             =   1440
      Width           =   12045
      _ExtentX        =   21246
      _ExtentY        =   11774
      _Version        =   393216
      BackColorFixed  =   14737632
      BackColorBkg    =   15790320
      SelectionMode   =   1
      Appearance      =   0
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
   Begin AdminCred.ctlBoton BtnCerrar 
      Height          =   270
      Left            =   10830
      TabIndex        =   3
      Top             =   8490
      Width           =   1260
      _ExtentX        =   2223
      _ExtentY        =   476
   End
   Begin AdminCred.ctlBoton BtnConsultar 
      Default         =   -1  'True
      Height          =   270
      Left            =   1530
      TabIndex        =   2
      Top             =   960
      Width           =   1260
      _ExtentX        =   2223
      _ExtentY        =   476
   End
   Begin MSComCtl2.DTPicker dpFechaPago 
      Height          =   285
      Left            =   90
      TabIndex        =   1
      Top             =   960
      Width           =   1335
      _ExtentX        =   2355
      _ExtentY        =   503
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
      Format          =   16842753
      CurrentDate     =   38675
   End
   Begin MSComctlLib.ProgressBar pbarConsultar 
      Height          =   195
      Left            =   5160
      TabIndex        =   9
      Top             =   8910
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
      TabIndex        =   10
      Top             =   8835
      Width           =   12150
      _ExtentX        =   21431
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
   Begin VB.Label Label1 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "&Fecha de pago:"
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
      TabIndex        =   0
      Top             =   750
      Width           =   1140
   End
   Begin VB.Label Label2 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Total Cantidad:"
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
      Left            =   180
      TabIndex        =   20
      Top             =   8580
      Width           =   1170
   End
   Begin VB.Label lbTotCantidad 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "$0.00"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FF6060&
      Height          =   180
      Left            =   1410
      TabIndex        =   19
      Top             =   8580
      Width           =   405
   End
   Begin VB.Label Label5 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Total Capital:"
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
      Left            =   3240
      TabIndex        =   18
      Top             =   8310
      Width           =   1035
   End
   Begin VB.Label lbTotCapital 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "$0.00"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FF6060&
      Height          =   180
      Left            =   4530
      TabIndex        =   17
      Top             =   8310
      Width           =   405
   End
   Begin VB.Label Label9 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Total Intereses:"
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
      Left            =   3240
      TabIndex        =   16
      Top             =   8580
      Width           =   1230
   End
   Begin VB.Label lbTotIntereses 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "$0.00"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FF6060&
      Height          =   180
      Left            =   4530
      TabIndex        =   15
      Top             =   8580
      Width           =   405
   End
   Begin VB.Label Label12 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Total Recargos:"
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
      Left            =   6720
      TabIndex        =   14
      Top             =   8310
      Width           =   1200
   End
   Begin VB.Label lbTotRecargos 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "$0.00"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FF6060&
      Height          =   180
      Left            =   8010
      TabIndex        =   13
      Top             =   8310
      Width           =   405
   End
   Begin VB.Label Label14 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "Total Pagos:"
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
      Left            =   180
      TabIndex        =   12
      Top             =   8310
      Width           =   960
   End
   Begin VB.Label lbNoPagos 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "0"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FF6060&
      Height          =   180
      Left            =   1410
      TabIndex        =   11
      Top             =   8310
      Width           =   90
   End
End
Attribute VB_Name = "frmBitacora"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private bCerrarForm As Boolean
Private lNoPagos As Long, lCantidad As Long, lCapital As Long, lIntereses As Long, lRecargos As Long

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

Private Sub BtnConsultar_BotonClick()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset, lNoRegs As Long, lTotalNoPagos As Long

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call BorrarFilasGrid
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select   count(*)       as no_regs, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(cantidad)  as cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadocap) as capital," & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadoint) as intereses, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         sum(pagadorec) as recargos, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem          as cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb          as cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         frealdep       as frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         secuenciaim    as secim " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     mp " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    frealdep = '" & Format(dpFechaPago.Value, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgcb    is not null " & vbNewLine
    sCadenaSQL = sCadenaSQL & "group by cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         secuenciaim " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         secuenciaim "
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            MsgBox "No se encontró información para la fecha proporcionada...", vbInformation + vbOKOnly, TITULO_MENSAJE
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
            lbNoPagos.Caption = "0"
            lbTotCantidad.Caption = "$0.00"
            lbTotCapital.Caption = "$0.00"
            lbTotIntereses.Caption = "$0.00"
            lbTotRecargos.Caption = "$0.00"
            lNoPagos = 0
            lCantidad = 0
            lCapital = 0
            lIntereses = 0
            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call LlenarGrid(oRstConsultar)
                oRstConsultar.IrAlRegSiguiente
            Wend
            
'            fgPagos.MergeCells = 1
'            fgPagos.MergeCol(0) = False
'            fgPagos.MergeCol(1) = True
'            fgPagos.MergeCol(2) = True
'            fgPagos.MergeCol(3) = True
'            fgPagos.MergeCol(4) = False
'            fgPagos.MergeCol(5) = False
'            fgPagos.MergeCol(6) = False
'            fgPagos.MergeCol(7) = False
'            fgPagos.MergeCol(8) = False
'            fgPagos.MergeCol(9) = False
            
            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
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
    
    bCerrarForm = False
    BtnConsultar.Texto = "&Consultar"
    BtnCerrar.Texto = "&Cerrar"
    dpFechaPago.Value = Date
    Call InicializaGrid
    'Call LlenarGrid
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub InicializaGrid()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgPagos
        .Rows = 1
        .Cols = 10
        .Row = 0
        .RowHeight(0) = 270
        
        .Col = 0
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol1.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "No."
        .ColWidth(0) = 500
        
        .Col = 1
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Empresa"
        .ColWidth(1) = 800
        
        .Col = 2
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Cta. Bancaria"
        .ColWidth(2) = 1200
        
        .Col = 3
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Fecha de Pago"
        .ColWidth(3) = 1200
        
        .Col = 4
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "No. de pagos"
        .ColWidth(4) = 1100
        
        .Col = 5
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Cantidad"
        .ColWidth(5) = 1550
        
        .Col = 6
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Capital"
        .ColWidth(6) = 1550
        
        .Col = 7
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Intereses"
        .ColWidth(7) = 1550
        
        .Col = 8
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Recargos"
        .ColWidth(8) = 1550
        
        .Col = 9
        .CellAlignment = flexAlignCenterCenter
        '.CellPictureAlignment = flexAlignCenterCenter
        'Set .CellPicture = pbCol2.Picture
        '.CellForeColor = vbWhite
        .CellFontBold = True
        .Text = "Secuencia"
        .ColWidth(9) = 900
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Public Sub LlenarGrid(ByVal poRst As clsoAdoRecordset)
    Dim vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgPagos
            .Rows = .Rows + 1
            .Row = .Rows - 1
            
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
            .CellBackColor = vbBlack
            '.CellFontBold = True
            .Text = CStr(.Row) & " "
            
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
            .CellAlignment = flexAlignCenterCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("frealdep").Value), "", poRst.ObjSetRegistros.Fields("frealdep").Value)
            
            .Col = 4
            .CellAlignment = flexAlignRightCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), "0", poRst.ObjSetRegistros.Fields("no_regs").Value)
            lNoPagos = lNoPagos + IIf(IsNull(poRst.ObjSetRegistros.Fields("no_regs").Value), 0, poRst.ObjSetRegistros.Fields("no_regs").Value)
            lbNoPagos.Caption = CStr(lNoPagos)
            
            .Col = 5
            .CellAlignment = flexAlignRightCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
            lCantidad = lCantidad + IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), 0, poRst.ObjSetRegistros.Fields("cantidad").Value)
            lbTotCantidad.Caption = Format(CStr(lCantidad), "$###,###,###,###,###,##0.00")
            
            .Col = 6
            .CellAlignment = flexAlignRightCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("capital").Value), "$0.00", poRst.ObjSetRegistros.Fields("capital").Value), "$###,###,###,###,###,##0.00")
            lCapital = lCapital + IIf(IsNull(poRst.ObjSetRegistros.Fields("capital").Value), 0, poRst.ObjSetRegistros.Fields("capital").Value)
            lbTotCapital.Caption = Format(CStr(lCapital), "$###,###,###,###,###,##0.00")
            
            .Col = 7
            .CellAlignment = flexAlignRightCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("intereses").Value), "$0.00", poRst.ObjSetRegistros.Fields("intereses").Value), "$###,###,###,###,###,##0.00")
            lIntereses = lIntereses + IIf(IsNull(poRst.ObjSetRegistros.Fields("intereses").Value), 0, poRst.ObjSetRegistros.Fields("intereses").Value)
            lbTotIntereses.Caption = Format(CStr(lIntereses), "$###,###,###,###,###,##0.00")
            
            .Col = 8
            .CellAlignment = flexAlignRightCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("recargos").Value), "$0.00", poRst.ObjSetRegistros.Fields("recargos").Value), "$###,###,###,###,###,##0.00")
            lRecargos = lRecargos + IIf(IsNull(poRst.ObjSetRegistros.Fields("recargos").Value), 0, poRst.ObjSetRegistros.Fields("recargos").Value)
            lbTotRecargos.Caption = Format(CStr(lRecargos), "$###,###,###,###,###,##0.00")
            
            .Col = 9
            .CellAlignment = flexAlignCenterCenter
            .CellForeColor = vColorFrente
            .CellBackColor = vColorFondo
            .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secim").Value), "", poRst.ObjSetRegistros.Fields("secim").Value)
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub BorrarFilasGrid()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    fgPagos.Rows = 1
    fgPagos.Refresh
    
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

