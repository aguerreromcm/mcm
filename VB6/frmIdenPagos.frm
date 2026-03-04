VERSION 5.00
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{BDC217C8-ED16-11CD-956C-0000C04E4C0A}#1.1#0"; "TABCTL32.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmIdenPagos 
   BackColor       =   &H00FFF9F9&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Identificación de Pagos"
   ClientHeight    =   8550
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9900
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   8550
   ScaleWidth      =   9900
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox pbCesto 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   195
      Left            =   2790
      ScaleHeight     =   195
      ScaleWidth      =   225
      TabIndex        =   17
      Top             =   10320
      Visible         =   0   'False
      Width           =   225
   End
   Begin AdminCred.ctlFiltroConciliacion ctlFiltroConciliacion1 
      Height          =   2535
      Left            =   0
      TabIndex        =   14
      Top             =   720
      Width           =   9900
      _ExtentX        =   17463
      _ExtentY        =   4471
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
      Left            =   8640
      TabIndex        =   7
      Top             =   7800
      Width           =   1000
   End
   Begin VB.PictureBox pbSel 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   2130
      ScaleHeight     =   210
      ScaleWidth      =   210
      TabIndex        =   6
      Top             =   10320
      Visible         =   0   'False
      Width           =   210
   End
   Begin VB.PictureBox pbSelNo 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   2460
      ScaleHeight     =   210
      ScaleWidth      =   210
      TabIndex        =   5
      Top             =   10320
      Visible         =   0   'False
      Width           =   210
   End
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9900
      TabIndex        =   0
      Top             =   0
      Width           =   9900
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   360
         Picture         =   "frmIdenPagos.frx":0000
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   18
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
         Left            =   9600
         TabIndex        =   3
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
         Left            =   8430
         TabIndex        =   2
         Top             =   180
         Width           =   1170
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Identificación de Pagos"
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
         TabIndex        =   1
         Top             =   60
         Width           =   4950
      End
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   4
      Top             =   8265
      Width           =   9900
      _ExtentX        =   17463
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de eliminación de pagos "
            TextSave        =   "Módulo de eliminación de pagos "
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
   Begin TabDlg.SSTab sstEliminacion 
      Height          =   4935
      Left            =   30
      TabIndex        =   8
      Top             =   3330
      Width           =   9825
      _ExtentX        =   17330
      _ExtentY        =   8705
      _Version        =   393216
      Tabs            =   1
      TabsPerRow      =   1
      TabHeight       =   520
      BackColor       =   16775673
      BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
         Name            =   "Verdana"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      TabCaption(0)   =   "Lista de Pagos"
      TabPicture(0)   =   "frmIdenPagos.frx":05D5
      Tab(0).ControlEnabled=   -1  'True
      Tab(0).Control(0)=   "lbRegsTab1"
      Tab(0).Control(0).Enabled=   0   'False
      Tab(0).Control(1)=   "lbDatoNoRegsTab1"
      Tab(0).Control(1).Enabled=   0   'False
      Tab(0).Control(2)=   "lbMontoTab1"
      Tab(0).Control(2).Enabled=   0   'False
      Tab(0).Control(3)=   "Label13"
      Tab(0).Control(3).Enabled=   0   'False
      Tab(0).Control(4)=   "Label1"
      Tab(0).Control(4).Enabled=   0   'False
      Tab(0).Control(5)=   "Label5"
      Tab(0).Control(5).Enabled=   0   'False
      Tab(0).Control(6)=   "fgPagos"
      Tab(0).Control(6).Enabled=   0   'False
      Tab(0).ControlCount=   7
      Begin MSFlexGridLib.MSFlexGrid fgPagos 
         Height          =   3675
         Left            =   60
         TabIndex        =   9
         Top             =   720
         Width           =   9735
         _ExtentX        =   17171
         _ExtentY        =   6482
         _Version        =   393216
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
      Begin VB.Label Label5 
         Alignment       =   2  'Center
         BackColor       =   &H000000FF&
         BorderStyle     =   1  'Fixed Single
         Caption         =   "Pagos No Importados"
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
         Height          =   240
         Left            =   60
         TabIndex        =   16
         Top             =   480
         Width           =   9705
      End
      Begin VB.Label Label1 
         Alignment       =   2  'Center
         BackStyle       =   0  'Transparent
         Caption         =   "PAGOS IDENTIFICADOS"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00008000&
         Height          =   180
         Left            =   90
         TabIndex        =   15
         Top             =   -150
         Width           =   9615
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
         Left            =   2985
         TabIndex        =   13
         Top             =   4560
         Width           =   525
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
         ForeColor       =   &H000000FF&
         Height          =   195
         Left            =   3585
         TabIndex        =   12
         Top             =   4560
         Width           =   540
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
         ForeColor       =   &H000000FF&
         Height          =   195
         Left            =   1380
         TabIndex        =   11
         Top             =   4560
         Width           =   120
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
         Left            =   90
         TabIndex        =   10
         Top             =   4560
         Width           =   1260
      End
   End
End
Attribute VB_Name = "frmIdenPagos"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False

Option Explicit

Private bCerrarForm As Boolean
Private dNoRegs As Long, dMonto As Double
Private SitGpo As String

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



Private Sub ctlFiltroConciliacion1_ClickBuscar()
    Dim oRstConciliar As New clsoAdoRecordset
    Dim sCadenaSQL As String, sMensaje As String

    On Error GoTo RutinaError
    
        Screen.MousePointer = vbHourglass
        DoEvents
        sbBarraEstado.Panels(1).Text = "Iniciando consulta..."
        dNoRegs = 0
        dMonto = 0
        sstEliminacion.Tab = 0
        Call BorrarFilasGrids
        lbDatoNoRegsTab1.Caption = "0"
        lbMontoTab1.Caption = "$0.00"
    
        With ctlFiltroConciliacion1
            cmdCerrar.Enabled = False
            ctlFiltroConciliacion1.Habilitado = False
            
            Call BuscarPagos(.Empresa, .FechaPago, .TipoCliente, .Codigo, .Nombre, .CtaBancaria)
            
            Screen.MousePointer = vbDefault
            sMensaje = ""
            sMensaje = sMensaje & "Se encontraron un total de " & CStr(CDbl(lbDatoNoRegsTab1.Caption)) & " pagos..." & vbNewLine & vbNewLine
            sMensaje = sMensaje & "     " & lbDatoNoRegsTab1.Caption & vbTab & " Pagos No Importados" & vbNewLine
            MsgBox sMensaje, vbInformation + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            

            sbBarraEstado.Panels(1).Text = "Se encontraron " & CStr(CDbl(lbDatoNoRegsTab1.Caption)) & " pagos..."
            
            
            cmdCerrar.Enabled = True
            ctlFiltroConciliacion1.Habilitado = True
        End With

    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub BuscarPagos(ByVal psEmpresa As String, ByVal psFechaPago As String, ByVal psTipoCliente As String, ByVal psCodigo As String, ByVal psNombre As String, ByVal psCtaBancaria As String)
    Dim oRstConsultar As New clsoAdoRecordset
    Dim sCadenaSQL As String
    Dim sCondEmpresa As String, sCondFechaPago As String, sCondTipoCliente As String
    Dim sCondCodigo As String, sCondNombre1 As String, sCondNombre2 As String, sCondCtaBancaria As String
    
    Dim bValor As Boolean
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    '------------------------------------------------------------------------------------------------------------------------
    '-----                          Procesamos la información para obtener los pagos NO IMPORTADOS                      -----
    '------------------------------------------------------------------------------------------------------------------------
    
    Call InicializaGrid
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select        * " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from          pdi " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where         fdeposito = '" & Format(ctlFiltroConciliacion1.FechaPago, "yyyy/mm/dd") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgem = 'EMPFIN' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           estatus = 'RE' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by      cdgem, " & vbNewLine
    'sCadenaSQL = sCadenaSQL & "              cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cantidad "
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----

            While Not oRstConsultar.FinDeArchivo
                lbDatoNoRegsTab1.Caption = CStr(CInt(lbDatoNoRegsTab1.Caption) + 1)
                Call PonerPagos(oRstConsultar)
                oRstConsultar.IrAlRegSiguiente
            Wend


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



Private Sub BorrarFilasGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    fgPagos.Rows = 1
    fgPagos.Refresh
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub


Private Sub ctlFiltroConciliacion1_ClickCodigo()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
        ctlFiltroConciliacion1.OptCodigo = False
        ctlFiltroConciliacion1.OptNombre = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickCtaBancaria()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptCtaBancaria = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickEmpresa()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptEmpresa = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickFechaPago()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptFechaPago = True
End Sub

Private Sub ctlFiltroConciliacion1_ClickNombre()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptNombre = False
    ctlFiltroConciliacion1.OptCodigo = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickSinFiltro()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptCodigo = True
    ctlFiltroConciliacion1.OptCtaBancaria = False
    ctlFiltroConciliacion1.OptEmpresa = True
    ctlFiltroConciliacion1.OptFechaPago = True
    ctlFiltroConciliacion1.OptNombre = False
    ctlFiltroConciliacion1.OptTipoCliente = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickTipoCliente()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptTipoCliente = False
End Sub


Private Sub fgPagos_DblClick()
    On Error GoTo RutinaError

    If (Trim(fgPagos.TextMatrix(fgPagos.Row, 1)) <> "" And fgPagos.Row <> 0 And IsNumeric(fgPagos.TextMatrix(fgPagos.Row, 0))) Then
        
        sEmpresa = fgPagos.TextMatrix(fgPagos.Row, 1)
        fdPago = fgPagos.TextMatrix(fgPagos.Row, 2)
        sCantidad = Replace(Replace(fgPagos.TextMatrix(fgPagos.Row, 5), "$", ""), ",", "")
        sCtaBco = fgPagos.TextMatrix(fgPagos.Row, 4)
        sSecuencia = fgPagos.TextMatrix(fgPagos.Row, 6)
        
        'fdPago = Date   '--- CAMBIO 07/10/2010 --- JOSE ANTONIO RENDON TORRES --- VALIDAR CONTRA FECHA SERVER --- INICIO ---'
        
        bIdentifica = True
        bResIdenPago = False
        frmRegPagos.Show 1, Me
        'Call ctlFiltroConciliacion1_ClickBuscar
        If bResIdenPago Then
            'fgPagos.RemoveItem CLng(fgPagos.Row)
            Call ctlFiltroConciliacion1_ClickBuscar
        End If
        'Call InicializaGrid
    End If

    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    sstEliminacion.Tab = 0
    Call InicializaGrid
        
    '-----   La eliminación de pagos solo podrá ser por archivo importado   -----
    With ctlFiltroConciliacion1
        .OptCodigo = False
        .OptCtaBancaria = False
        .OptEmpresa = False
        .OptFechaPago = True
        .OptNombre = False
        .OptTipoCliente = False
    End With

    ctlFiltroConciliacion1.QuitarFiltro = False

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



Private Sub InicializaGrid()
    Dim vColorFrente As Variant, vColorFondo As Variant

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgPagos
        .Rows = 1
        .Row = .Rows - 1
        
        If (.Row Mod 2 = 1) Then
            vColorFrente = vbBlack
            vColorFondo = &HFFF5F5
            'vColorFondo = vbWhite
        Else
            vColorFrente = vbBlack
            vColorFondo = vbWhite
        End If
        
                .Cols = 10
                
                .Col = 0
                .ColWidth(0) = 500
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "No."
                
                .Col = 1
                .ColWidth(1) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Empresa"
                
                .Col = 2
                .ColWidth(2) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Fecha pago"
                
                .Col = 3
                .ColWidth(3) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Referencia"
                
                .Col = 4
                .ColWidth(4) = 1200
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cta. Bancaria"
                
                .Col = 5
                .ColWidth(5) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cantidad"
                
                .Col = 6
                .ColWidth(6) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Secuencia"
                
                .Col = 7
                .ColWidth(7) = 7500
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Observaciones"
                
                .Col = 8
                .ColWidth(8) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "SecuenciaIM"
                
                .Col = 9
                .ColWidth(9) = 2100
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Fecha carga"
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerPagos(ByVal poRst As clsoAdoRecordset)
    Dim vColorFrente As Variant, vColorFondo As Variant, sFechaCarga As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    If (fgPagos.Row Mod 2 = 1) Then
        vColorFrente = vbBlack
        vColorFondo = &HFFF5F5
        'vColorFondo = vbWhite
    Else
        vColorFrente = vbBlack
        vColorFondo = vbWhite
    End If

    With fgPagos
        .Rows = .Rows + 1
        .Row = .Rows - 1
                
                'lbTipoPago.Caption = "Pagos No Importados" & vbNewLine & " (Empresa: EMPFIN, Fecha pago: " & ctlFiltroConciliacion1.FechaPago & ")"
            
                .Col = 0
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
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
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fdeposito").Value), "", poRst.ObjSetRegistros.Fields("fdeposito").Value)
                
                .Col = 3
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
                
                .Col = 4
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
                
                .Col = 5
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 6
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuencia").Value), "", poRst.ObjSetRegistros.Fields("secuencia").Value)
                
                .Col = 7
                .CellAlignment = flexAlignLeftCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("descripcion").Value), "", Replace(Replace(Replace(Replace(poRst.ObjSetRegistros.Fields("descripcion").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", ""))

                .Col = 8
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuenciaim").Value), "", poRst.ObjSetRegistros.Fields("secuenciaim").Value)
                
                .Col = 9
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fechaim").Value), "", poRst.ObjSetRegistros.Fields("fechaim").Value)

    End With

    dNoRegs = dNoRegs + 1
    dMonto = dMonto + poRst.ObjSetRegistros.Fields("cantidad").Value
    DoEvents
    lbDatoNoRegsTab1.Caption = CStr(dNoRegs)
    lbMontoTab1.Caption = Format(dMonto, "$###,###,###,##0.00")

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub
