VERSION 5.00
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{BDC217C8-ED16-11CD-956C-0000C04E4C0A}#1.1#0"; "TABCTL32.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmConciliacion 
   AutoRedraw      =   -1  'True
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Conciliación de Pagos"
   ClientHeight    =   8475
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9945
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmConciliacion.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   8475
   ScaleWidth      =   9945
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9945
      TabIndex        =   20
      Top             =   0
      Width           =   9945
      Begin VB.PictureBox Picture2 
         Height          =   735
         Left            =   360
         Picture         =   "frmConciliacion.frx":08CA
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   25
         Top             =   0
         Width           =   1095
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Conciliación de Pagos"
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
         TabIndex        =   23
         Top             =   60
         Width           =   4740
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
         TabIndex        =   22
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
         TabIndex        =   21
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
      BackColor       =   &H00FFF9F9&
      Height          =   7455
      Left            =   0
      ScaleHeight     =   7395
      ScaleWidth      =   9885
      TabIndex        =   2
      Top             =   735
      Width           =   9945
      Begin AdminCred.ctlFiltroConciliacion ctlFiltroConciliacion1 
         Height          =   2535
         Left            =   0
         TabIndex        =   24
         Top             =   0
         Width           =   9900
         _ExtentX        =   17463
         _ExtentY        =   4471
      End
      Begin MSComDlg.CommonDialog cdlgConciliacion 
         Left            =   30
         Top             =   2160
         _ExtentX        =   847
         _ExtentY        =   847
         _Version        =   393216
      End
      Begin VB.PictureBox pbSelNo 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         BorderStyle     =   0  'None
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   2250
         Picture         =   "frmConciliacion.frx":0E9F
         ScaleHeight     =   210
         ScaleWidth      =   210
         TabIndex        =   14
         Top             =   6990
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
         Left            =   1920
         Picture         =   "frmConciliacion.frx":1149
         ScaleHeight     =   210
         ScaleWidth      =   210
         TabIndex        =   13
         Top             =   6990
         Visible         =   0   'False
         Width           =   210
      End
      Begin VB.PictureBox Picture1 
         Height          =   30
         Left            =   3090
         ScaleHeight     =   30
         ScaleWidth      =   30
         TabIndex        =   9
         Top             =   270
         Width           =   30
      End
      Begin TabDlg.SSTab sstConciliacion 
         Height          =   4365
         Left            =   60
         TabIndex        =   5
         Top             =   2550
         Width           =   9795
         _ExtentX        =   17277
         _ExtentY        =   7699
         _Version        =   393216
         Tabs            =   4
         TabsPerRow      =   4
         TabHeight       =   520
         BackColor       =   16775673
         BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         TabCaption(0)   =   "Por conciliar"
         TabPicture(0)   =   "frmConciliacion.frx":13F3
         Tab(0).ControlEnabled=   -1  'True
         Tab(0).Control(0)=   "Label13"
         Tab(0).Control(0).Enabled=   0   'False
         Tab(0).Control(1)=   "lbMontoTab1"
         Tab(0).Control(1).Enabled=   0   'False
         Tab(0).Control(2)=   "lbDatoNoRegsTab1"
         Tab(0).Control(2).Enabled=   0   'False
         Tab(0).Control(3)=   "lbRegsTab1"
         Tab(0).Control(3).Enabled=   0   'False
         Tab(0).Control(4)=   "fgPorConciliar"
         Tab(0).Control(4).Enabled=   0   'False
         Tab(0).Control(5)=   "cmdSelTodos"
         Tab(0).Control(5).Enabled=   0   'False
         Tab(0).Control(6)=   "cmdQuitarSel"
         Tab(0).Control(6).Enabled=   0   'False
         Tab(0).ControlCount=   7
         TabCaption(1)   =   "Conciliados"
         TabPicture(1)   =   "frmConciliacion.frx":140F
         Tab(1).ControlEnabled=   0   'False
         Tab(1).Control(0)=   "fgConciliados"
         Tab(1).ControlCount=   1
         TabCaption(2)   =   "No conciliados"
         TabPicture(2)   =   "frmConciliacion.frx":142B
         Tab(2).ControlEnabled=   0   'False
         Tab(2).Control(0)=   "fgNoConciliados"
         Tab(2).ControlCount=   1
         TabCaption(3)   =   "Distribuidos"
         TabPicture(3)   =   "frmConciliacion.frx":1447
         Tab(3).ControlEnabled=   0   'False
         Tab(3).Control(0)=   "fgDistribuidos"
         Tab(3).ControlCount=   1
         Begin VB.CommandButton cmdQuitarSel 
            Caption         =   "Q&uitar selección"
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
            Left            =   8010
            TabIndex        =   8
            Top             =   3960
            Visible         =   0   'False
            Width           =   1700
         End
         Begin VB.CommandButton cmdSelTodos 
            Caption         =   "&Seleccionar todos"
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
            Left            =   6270
            TabIndex        =   7
            Top             =   3960
            Visible         =   0   'False
            Width           =   1700
         End
         Begin MSFlexGridLib.MSFlexGrid fgPorConciliar 
            Height          =   3525
            Left            =   0
            TabIndex        =   6
            Top             =   360
            Width           =   9705
            _ExtentX        =   17119
            _ExtentY        =   6218
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
         Begin MSFlexGridLib.MSFlexGrid fgConciliados 
            Height          =   3975
            Left            =   -74940
            TabIndex        =   10
            Top             =   360
            Width           =   9705
            _ExtentX        =   17119
            _ExtentY        =   7011
            _Version        =   393216
            SelectionMode   =   1
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
         Begin MSFlexGridLib.MSFlexGrid fgNoConciliados 
            Height          =   3975
            Left            =   -74940
            TabIndex        =   11
            Top             =   360
            Width           =   9705
            _ExtentX        =   17119
            _ExtentY        =   7011
            _Version        =   393216
            SelectionMode   =   1
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
         Begin MSFlexGridLib.MSFlexGrid fgDistribuidos 
            Height          =   3975
            Left            =   -74940
            TabIndex        =   12
            Top             =   360
            Width           =   9705
            _ExtentX        =   17119
            _ExtentY        =   7011
            _Version        =   393216
            SelectionMode   =   1
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
            TabIndex        =   18
            Top             =   4020
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
            TabIndex        =   17
            Top             =   4020
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
            TabIndex        =   16
            Top             =   4020
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
            TabIndex        =   15
            Top             =   4020
            Width           =   525
         End
      End
      Begin VB.CommandButton cmdConciliacion 
         Caption         =   "Conciliar &pagos..."
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
         Top             =   7050
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
         Left            =   8850
         TabIndex        =   3
         Top             =   7050
         Width           =   1000
      End
      Begin VB.CommandButton cmdExpExcel 
         Caption         =   "E&xportar a Excel..."
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
         TabIndex        =   19
         Top             =   7050
         Visible         =   0   'False
         Width           =   1600
      End
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   1
      Top             =   8190
      Width           =   9945
      _ExtentX        =   17542
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de conciliación de pagos "
            TextSave        =   "Módulo de conciliación de pagos "
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
Attribute VB_Name = "frmConciliacion"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False


Private bCerrarForm As Boolean
Private dNoRegs As Long, dMonto As Double
Private sIdentificador As String

Private Const NUM_COLS_PROCESADOS = 13
Private Const NUM_COLS_CONCILIADOS = 12
Private Const NUM_COLS_NOCONCILIADOS = 14
Private Const NUM_COLS_DISTRIBUIDOS = 12
Private Const NOMBRE_FONT = "Verdana"
Private Const TAMAÑO_FONT = 8

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

Private Sub cmdConciliacion_Click()
    Dim lRenglon As Long, sCadenaSQL As String
    Dim oRstObtPago As New clsoAdoRecordset, oRstObtRes As New clsoAdoRecordset
    Dim vColoFrente As Variant, vColorFondo As Variant
    Dim sCodigoGpo As String
    Dim Resultado As String
    
    'Dim acmd As New ADODB.Command  'AMGM 08DIC2009   Este comando se utiliza para la ejecución del SP
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    DoEvents
    cmdExpExcel.Visible = False
    cmdConciliacion.Visible = False
    cmdSelTodos.Visible = False
    cmdQuitarSel.Visible = False
    cmdCerrar.Enabled = False
    ctlFiltroConciliacion1.Habilitado = False
    pbarConciliacion.Value = 0
    pbarConciliacion.Max = lbDatoNoRegsTab1.Caption
    pbarConciliacion.Visible = True
    
    With fgPorConciliar
        sIdentificador = Format(Date, "DDMMYYYY") & Format(Time, "HHNNSS")
        oAccesoDatos.cnn.IniciarTrans
        For lRenglon = 1 To .Rows - 1
            .Row = lRenglon
            .Col = 1
            DoEvents
            pbarConciliacion.Value = lRenglon
            sbBarraEstado.Panels(1).Text = "Conciliando y distribuyendo pago no. " & CStr(lRenglon) & " de " & CStr(lbDatoNoRegsTab1.Caption) & "  (" & CStr(Format(((lRenglon) * 100) / lbDatoNoRegsTab1.Caption, "##0.00")) & "%)"
            
            If (.CellPicture = Me.pbSel.Picture) Then

                '-----   Ejecutamos el proceso de Conciliación para el pago en cuestión   -----
                sCadenaSQL = ""
                'sCadenaSQL = "spConciliacion_Rapida_MP('" & .TextMatrix(lRenglon, 3) & "', '" & .TextMatrix(lRenglon, 7) & "', '" & .TextMatrix(lRenglon, 8) & "', '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "', '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "', '" & sUsuarioApp & "', " & IIf(Mid(.TextMatrix(lRenglon, 6), 1, 1) = "G", "null", "'" & .TextMatrix(lRenglon, 7) & "'") & ", " & Replace(Replace(.TextMatrix(lRenglon, 12), "$", ""), ",", "") & ", '" & .TextMatrix(lRenglon, 16) & "', " & IIf(Mid(.TextMatrix(lRenglon, 6), 1, 1) = "I", "null", "'" & .TextMatrix(lRenglon, 7) & "'") & ", " & .TextMatrix(lRenglon, 9) & ", null, " & .TextMatrix(lRenglon, 15) & ", 0, '" & .TextMatrix(lRenglon, 13) & "', '" & .TextMatrix(lRenglon, 10) & "', '" & .TextMatrix(lRenglon, 2) & "', " & Trim(.TextMatrix(lRenglon, 0)) & ", " & sIdentificador & ", " & .TextMatrix(lRenglon, 17) & ", '" & .TextMatrix(lRenglon, 18) & "', '" & sUsuarioApp & "')"
                sCadenaSQL = "spRedistribucionPagos('" & .TextMatrix(lRenglon, 3) & "', '" & .TextMatrix(lRenglon, 7) & "', '" & .TextMatrix(lRenglon, 8) & "', '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "', '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "', " & .TextMatrix(lRenglon, 9) & ", '" & .TextMatrix(lRenglon, 16) & "', " & Replace(Replace(.TextMatrix(lRenglon, 12), "$", ""), ",", "") & ",'" & .TextMatrix(lRenglon, 13) & "', '" & sUsuarioApp & "', " & sIdentificador & ")"
                'CALL spRedistribucionPagos('FINFIN','201139','06','G','23/05/2007',4,'02',11984,'06','JART')
                '     spRedistribucionPagos('SIMPLE', '006003', '01', 'G', '2009/12/30', 16, '02', 3.00,'05', 'RGCR')
                'spConciliacion_Rapida_MP('SIMPLE', '001008', '01', 'G', '2009/11/27', 'RGCR', null, 8260.00, '01', '001008', 1, null, 4.5, 0, '05', '1', '03/12/2009 11:40:01 am', 1, 03122009114250, 16, 'S', 'RGCR')
                
                oAccesoDatos.cnn.Ejecutar sCadenaSQL
                
'                    Set acmd = Nothing
'                    With acmd
'                        '.CommandText = "spImportaPagoSOF"
'                        .CommandText = "spConciliacion_Rapida_MP"
'                        .CommandType = adCmdStoredProc
'                        .ActiveConnection = oAccesoDatos.cnn.ObjConexion
'
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Empresa
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'CDGCLNS
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Ciclo
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'CLNS
'                        .Parameters.Append .CreateParameter(, adDate, adParamInput, 30)  'Fecha Real
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'CDGPE
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'CDGCL
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'CANTIDAD
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'SECUENCIA
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'CDGNS
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'PERIODO
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'MULTPER
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'TASA
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'TIPOOPER
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'CDGCB
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'SECUEIM
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'FECCARGA VARCHAR
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'NOPAGO
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'IDENTIFICADOR
'                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'PLAZO
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'PERIODICIDAD
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'USER
'
'                        .Parameters.Append .CreateParameter(, adVarChar, adParamOutput, 1)  'Resultado de la Conciliacion
'
'                        .Parameters(0) = .TextMatrix(lRenglon, 3)
'                        .Parameters(1) = .TextMatrix(lRenglon, 7)
'                        .Parameters(2) = .TextMatrix(lRenglon, 8)
'                        .Parameters(3) = Mid(.TextMatrix(lRenglon, 6), 1, 1)
'                        .Parameters(4) = Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd")
'                        .Parameters(5) = sUsuarioApp
'                        .Parameters(6) = IIf(Mid(.TextMatrix(lRenglon, 6), 1, 1) = "G", "null", "'" & .TextMatrix(lRenglon, 7))
'                        .Parameters(7) = Replace(Replace(.TextMatrix(lRenglon, 12), "$", ""), ",", "")
'                        .Parameters(8) = .TextMatrix(lRenglon, 16)
'                        .Parameters(9) = IIf(Mid(.TextMatrix(lRenglon, 6), 1, 1) = "I", "null", "'" & .TextMatrix(lRenglon, 7) & "'")
'                        .Parameters(10) = .TextMatrix(lRenglon, 9)
'                        .Parameters(11) = Null
'                        .Parameters(12) = .TextMatrix(lRenglon, 15)
'                        .Parameters(13) = 0
'                        .Parameters(14) = .TextMatrix(lRenglon, 13)
'                        .Parameters(15) = .TextMatrix(lRenglon, 10)
'                        .Parameters(16) = .TextMatrix(lRenglon, 2)
'                        .Parameters(17) = Trim(.TextMatrix(lRenglon, 0))
'                        .Parameters(18) = sIdentificador
'                        .Parameters(19) = .TextMatrix(lRenglon, 17)
'                        .Parameters(20) = .TextMatrix(lRenglon, 18)
'                        .Parameters(21) = sUsuarioApp
'
'                    End With
'                    acmd.Execute
                    'MsgBox "Resultado = " & acmd.Parameters(9)

'                    If acmd.Parameters(9) <> 1 Then
'                            Set .CellPicture = pbNoImportado.Picture
'                    End If
                
                
            End If
        Next
        
'        For lRenglon = 1 To .Rows - 1
'            .Row = lRenglon
'            .Col = 1
'            DoEvents
'            pbarConciliacion.Value = lRenglon
'            sbBarraEstado.Panels(1).Text = "Actualizando pago no. " & CStr(lRenglon) & " de " & CStr(lbDatoNoRegsTab1.Caption) & "  (" & CStr(Format(((lRenglon) * 100) / lbDatoNoRegsTab1.Caption, "##0.00")) & "%)"
'
'            If (.CellPicture = Me.pbSel.Picture) Then
'                '-----   Ejecutamos el proceso de actualización del campo de conciliación   -----
'                sCadenaSQL = "ObtResConciliacion_mp('" & .TextMatrix(lRenglon, 3) & "', '" & .TextMatrix(lRenglon, 7) & "', '" & .TextMatrix(lRenglon, 8) & "', '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "', '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "', '" & sUsuarioApp & "', " & IIf(Mid(.TextMatrix(lRenglon, 6), 1, 1) = "G", "null", "'" & .TextMatrix(lRenglon, 7) & "'") & ", " & Replace(Replace(.TextMatrix(lRenglon, 12), "$", ""), ",", "") & ", '" & .TextMatrix(lRenglon, 16) & "', " & IIf(Mid(.TextMatrix(lRenglon, 6), 1, 1) = "I", "null", "'" & .TextMatrix(lRenglon, 7) & "'") & ", " & .TextMatrix(lRenglon, 9) & ", null, " & .TextMatrix(lRenglon, 15) & ", 0, '" & .TextMatrix(lRenglon, 13) & "', '" & .TextMatrix(lRenglon, 10) & "', '" & .TextMatrix(lRenglon, 2) & "', " & Trim(.TextMatrix(lRenglon, 0)) & ", " & sIdentificador & ")"
'                oAccesoDatos.cnn.Ejecutar sCadenaSQL
'            End If
'        Next
        
        'sCadenaSQL = "Actualiza_IMyMB_mp('" & Format(ctlFiltroConciliacion1.FechaPago, "YYYY/MM/DD") & "', '" & sUsuarioApp & "')"
        'sbBarraEstado.Panels(1).Text = "Finalizando actualización de pagos..."
        'oAccesoDatos.cnn.Ejecutar sCadenaSQL
        
        For lRenglon = 1 To .Rows - 1
            .Row = lRenglon
            .Col = 1
            DoEvents
            pbarConciliacion.Value = lRenglon
            sbBarraEstado.Panels(1).Text = "Obteniendo resultado pago no. " & CStr(lRenglon) & " de " & CStr(lbDatoNoRegsTab1.Caption) & "  (" & CStr(Format(((lRenglon) * 100) / lbDatoNoRegsTab1.Caption, "##0.00")) & "%)"
            If (.CellPicture = Me.pbSel.Picture) Then
                'sCadenaSQL = "select * from res_conc where cdgem = '" & .TextMatrix(lRenglon, 3) & "' and cdgclns = '" & .TextMatrix(lRenglon, 7) & "' and ciclo = '" & .TextMatrix(lRenglon, 8) & "' and clns = '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "' and fecha = '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "' and secuemp = '" & .TextMatrix(lRenglon, 16) & "' and secueim = '" & .TextMatrix(lRenglon, 10) & "' and identificador = " & sIdentificador
                
                sCadenaSQL = ""
                sCadenaSQL = sCadenaSQL & "SELECT CDGEM, CDGCLNS, CLNS, CDGNS, CDGCL, CICLO, PERIODO,SECUENCIA, CDGCB, TIPO, FREALDEP, SECUENCIAIM, CANTIDAD, CONCILIADO " & vbNewLine
                sCadenaSQL = sCadenaSQL & " ,CASE WHEN CONCILIADO = 'N' THEN ( " & vbNewLine
                sCadenaSQL = sCadenaSQL & " select DESCRIPCION from res_conc where cdgem = '" & .TextMatrix(lRenglon, 3) & "' and cdgclns = '" & .TextMatrix(lRenglon, 7) & "' and ciclo = '" & .TextMatrix(lRenglon, 8) & "' and clns = '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "' and fecha = '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "' and secuemp = '" & .TextMatrix(lRenglon, 16) & "'" & IIf(.TextMatrix(lRenglon, 10) = "", "", " and secueim = '" & .TextMatrix(lRenglon, 10) & "'") & " and identificador = " & sIdentificador & vbNewLine
                sCadenaSQL = sCadenaSQL & " ) " & vbNewLine
                sCadenaSQL = sCadenaSQL & " ELSE NULL " & vbNewLine
                sCadenaSQL = sCadenaSQL & " END DESCRIPCION " & vbNewLine
                sCadenaSQL = sCadenaSQL & " FROM MP " & vbNewLine
                sCadenaSQL = sCadenaSQL & " WHERE  cdgem = '" & .TextMatrix(lRenglon, 3) & "' and cdgclns = '" & .TextMatrix(lRenglon, 7) & "' and ciclo = '" & .TextMatrix(lRenglon, 8) & "' and clns = '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "' and frealdep = '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "' and secuencia = '" & .TextMatrix(lRenglon, 16) & "'" & IIf(.TextMatrix(lRenglon, 10) = "", "", " and secuenciaim = '" & .TextMatrix(lRenglon, 10) & "'") & " " & vbNewLine
                sCadenaSQL = sCadenaSQL & "        and periodo = " & .TextMatrix(lRenglon, 9)

                
                oRstObtPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
                Select Case oRstObtPago.HayRegistros
                    Case 0  '-----   La consulta no retorno registros.   -----
                        Screen.MousePointer = vbDefault
                        MsgBox "No se pudo precisar el estado del pago: " & .TextMatrix(lRenglon, 7), vbInformation + vbOKOnly, TITULO_MENSAJE
                        Screen.MousePointer = vbHourglass
                        oRstObtPago.Cerrar
                    Case 1  '-----   Hay registros.                       -----
                        Select Case oRstObtPago.ObjSetRegistros.Fields("conciliado").Value
                            Case "C"    '-----   Conciliado   -----
                                vColorFrente = vbBlack

                                If (fgConciliados.Row Mod 2 = 0) Then vColorFondo = &HFFF0F0 Else vColorFondo = vbWhite
                                fgConciliados.Rows = fgConciliados.Rows + 1
                                fgConciliados.Row = fgConciliados.Rows - 1
                                fgConciliados.Col = 0
                                fgConciliados.Text = CStr(fgConciliados.Row)
                                fgConciliados.Col = 1
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignRightCenter
                                fgConciliados.Text = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
                                fgConciliados.Col = 2
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("cdgem").Value
                                fgConciliados.Col = 3
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignRightCenter
                                'fgConciliados.Text = Format(oRstObtPago.ObjSetRegistros.Fields("fecha").Value, "dd/mm/yyyy")
                                fgConciliados.Text = Format(oRstObtPago.ObjSetRegistros.Fields("frealdep").Value, "dd/mm/yyyy")
                                fgConciliados.Col = 4
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                fgConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 5)
                                fgConciliados.Col = 5
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                If (oRstObtPago.ObjSetRegistros.Fields("clns").Value = "G") Then
                                    fgConciliados.Text = "Grupal"
                                Else
                                    fgConciliados.Text = "Individual"
                                End If
                                fgConciliados.Col = 6
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("cdgclns").Value
                                fgConciliados.Col = 7
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("ciclo").Value
                                fgConciliados.Col = 8
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                fgConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 9)
                                fgConciliados.Col = 9
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                'fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secuemp").Value
                                fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secuencia").Value
                                fgConciliados.Col = 10
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignLeftCenter
                                fgConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 11)
                                fgConciliados.Col = 11
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignRightCenter
                                fgConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 12)
                                fgConciliados.Col = 12
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("cdgcb").Value
                                fgConciliados.Col = 13
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignCenterCenter
                                'fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secueim").Value
                                fgConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secuenciaim").Value
                                fgConciliados.Col = 14
                                fgConciliados.CellForeColor = vColoFrente
                                fgConciliados.CellBackColor = vColorFondo
                                fgConciliados.CellAlignment = flexAlignLeftCenter
                                fgConciliados.Text = ""
                            Case "N"    '-----   No conciliado   -----
                                vColorFrente = vbBlack

                                If (fgNoConciliados.Row Mod 2 = 0) Then vColorFondo = &HF0F0F0 Else vColorFondo = vbWhite
                                fgNoConciliados.Rows = fgNoConciliados.Rows + 1
                                fgNoConciliados.Row = fgNoConciliados.Rows - 1
                                fgNoConciliados.Col = 0
                                fgNoConciliados.Text = CStr(fgNoConciliados.Row)
                                fgNoConciliados.Col = 1
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignRightCenter
                                fgNoConciliados.Text = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
                                fgNoConciliados.Col = 2
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("cdgem").Value
                                fgNoConciliados.Col = 3
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignRightCenter
                                'fgNoConciliados.Text = Format(oRstObtPago.ObjSetRegistros.Fields("fecha").Value, "dd/mm/yyyy")
                                fgNoConciliados.Text = Format(oRstObtPago.ObjSetRegistros.Fields("frealdep").Value, "dd/mm/yyyy")
                                fgNoConciliados.Col = 4
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                fgNoConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 5)
                                fgNoConciliados.Col = 5
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                If (oRstObtPago.ObjSetRegistros.Fields("clns").Value = "G") Then
                                    fgNoConciliados.Text = "Grupal"
                                Else
                                    fgNoConciliados.Text = "Individual"
                                End If
                                fgNoConciliados.Col = 6
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("cdgclns").Value
                                fgNoConciliados.Col = 7
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("ciclo").Value
                                fgNoConciliados.Col = 8
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                fgNoConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 9)
                                fgNoConciliados.Col = 9
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                'fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secueim").Value
                                fgNoConciliados.Text = IIf(IsNull(oRstObtPago.ObjSetRegistros.Fields("secuenciaim").Value), "", oRstObtPago.ObjSetRegistros.Fields("secuenciaim").Value)
                                fgNoConciliados.Col = 10
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignLeftCenter
                                fgNoConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 11)
                                fgNoConciliados.Col = 11
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignRightCenter
                                fgNoConciliados.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 12)
                                fgNoConciliados.Col = 12
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("cdgcb").Value
                                fgNoConciliados.Col = 13
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignCenterCenter
                                'fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secuemp").Value
                                fgNoConciliados.Text = oRstObtPago.ObjSetRegistros.Fields("secuencia").Value
                                fgNoConciliados.Col = 14
                                fgNoConciliados.CellForeColor = vColoFrente
                                fgNoConciliados.CellBackColor = vColorFondo
                                fgNoConciliados.CellAlignment = flexAlignLeftCenter
                                fgNoConciliados.Text = IIf(IsNull(oRstObtPago.ObjSetRegistros.Fields("descripcion").Value), "", oRstObtPago.ObjSetRegistros.Fields("descripcion").Value)
                            Case "D"    '-----   Distribuido   -----
                                vColorFrente = vbBlack

                                If (fgDistribuidos.Row Mod 2 = 0) Then vColorFondo = &HFFF0F0 Else vColorFondo = vbWhite
                                fgDistribuidos.Rows = fgDistribuidos.Rows + 1
                                fgDistribuidos.Row = fgDistribuidos.Rows - 1
                                fgDistribuidos.Col = 0
                                fgDistribuidos.Text = CStr(fgDistribuidos.Row)
                                fgDistribuidos.Col = 1
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignRightCenter
                                fgDistribuidos.Text = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
                                fgDistribuidos.Col = 2
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                fgDistribuidos.Text = oRstObtPago.ObjSetRegistros.Fields("cdgem").Value
                                fgDistribuidos.Col = 3
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignRightCenter
                                'fgDistribuidos.Text = Format(oRstObtPago.ObjSetRegistros.Fields("fecha").Value, "dd/mm/yyyy")
                                fgDistribuidos.Text = Format(oRstObtPago.ObjSetRegistros.Fields("frealdep").Value, "dd/mm/yyyy")
                                fgDistribuidos.Col = 4
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                fgDistribuidos.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 5)
                                fgDistribuidos.Col = 5
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                If (oRstObtPago.ObjSetRegistros.Fields("clns").Value = "G") Then
                                    fgDistribuidos.Text = "Grupal"
                                Else
                                    fgDistribuidos.Text = "Individual"
                                End If
                                fgDistribuidos.Col = 6
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                fgDistribuidos.Text = oRstObtPago.ObjSetRegistros.Fields("cdgclns").Value
                                fgDistribuidos.Col = 7
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                fgDistribuidos.Text = oRstObtPago.ObjSetRegistros.Fields("ciclo").Value
                                fgDistribuidos.Col = 8
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                fgDistribuidos.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 9)
                                fgDistribuidos.Col = 9
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                'fgDistribuidos.Text = oRstObtPago.ObjSetRegistros.Fields("secuemp").Value
                                fgDistribuidos.Text = oRstObtPago.ObjSetRegistros.Fields("secuencia").Value
                                fgDistribuidos.Col = 10
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignLeftCenter
                                fgDistribuidos.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 11)
                                fgDistribuidos.Col = 11
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignRightCenter
                                fgDistribuidos.Text = fgPorConciliar.TextMatrix(fgPorConciliar.Row, 12)
                                fgDistribuidos.Col = 12
                                fgDistribuidos.CellForeColor = vColoFrente
                                fgDistribuidos.CellBackColor = vColorFondo
                                fgDistribuidos.CellAlignment = flexAlignCenterCenter
                                fgDistribuidos.Text = oRstObtPago.ObjSetRegistros.Fields("cdgcb").Value
                        End Select

                        oRstObtPago.Cerrar
                    Case 2  '-----   El Query no se pudo ejecutar.        -----
                        Screen.MousePointer = vbDefault
                        MsgBox "Existe algun problema con la conexión a la base de datos..." & vbNewLine & "Consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
                        Screen.MousePointer = vbHourglass
                        oRstObtPago.Cerrar
                        Screen.MousePointer = vbDefault
                End Select
            End If
        Next
        
    End With
    
    cmdCerrar.Enabled = True
    oAccesoDatos.cnn.AceptarTrans
    
    Screen.MousePointer = vbDefault
    MsgBox "Se procesaron un total de " & lbDatoNoRegsTab1.Caption & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
    Screen.MousePointer = vbHourglass
    pbarConciliacion.Value = 0
    pbarConciliacion.Visible = False
    sbBarraEstado.Panels(1).Text = "Se procesaron " & lbDatoNoRegsTab1.Caption & " pagos..."
    cmdExpExcel.Visible = True
    ctlFiltroConciliacion1.Habilitado = True
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    oAccesoDatos.cnn.DeshacerTrans
    MensajeError Err
End Sub

Private Sub cmdExpExcel_Click()
    Dim AppExcel As New Excel.Application
    Dim LibroExcel As New Excel.Workbook
    Dim HojaExcel As New Excel.Worksheet
    Dim lContX As Long, lContY As Long, sRango As String
    Dim sLetra As String, sNumero As String
    Dim existe
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    cmdCerrar.Enabled = False
    ctlFiltroConciliacion1.Habilitado = False
    cmdExpExcel.Enabled = False
    
    cdlgConciliacion.Filter = "Archivos de Excel (*.xls)|*.xls"
    cdlgConciliacion.ShowSave
    
    existe = ""
    existe = Dir(cdlgConciliacion.FileName)
    
    Set LibroExcel = AppExcel.Workbooks.Add(xlWBATWorksheet)
    LibroExcel.Worksheets.Add
    LibroExcel.Worksheets.Add
    LibroExcel.Worksheets.Add
    
    '-----   Incluimos los pagos procesados en el archivo de Excel   -----
    pbarConciliacion.Value = 0
    If (fgPorConciliar.Rows = 1) Then
        pbarConciliacion.Max = 1
    Else
        pbarConciliacion.Max = fgPorConciliar.Rows - 1
    End If
    pbarConciliacion.Visible = True
    
    With fgPorConciliar
        Set HojaExcel = LibroExcel.Worksheets(1)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "Procesados"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 1 de 4) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarConciliacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 1 de 4) pago no. 0 de 0  (100%)"
                pbarConciliacion.Value = 1
            End If
            
            For lContX = 2 To NUM_COLS_PROCESADOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = &H404040     '----- Gris oscuro   -----
                    
                    Select Case .Col
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000"
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 11
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                        Case 12
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "$###,###,###,##0.00"
                        Case 13
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos Conciliados   -----
    pbarConciliacion.Value = 0
    If (fgConciliados.Rows = 1) Then
        pbarConciliacion.Max = 1
    Else
        pbarConciliacion.Max = fgConciliados.Rows - 1
    End If
    pbarConciliacion.Visible = True
    
    With fgConciliados
        Set HojaExcel = LibroExcel.Worksheets(2)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "Conciliados"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 2 de 4) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarConciliacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 2 de 4) pago no. 0 de 0  (100%)"
                pbarConciliacion.Value = 1
            End If
            
            For lContX = 1 To NUM_COLS_CONCILIADOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = &H404040     '----- Gris oscuro   -----
                    
                    Select Case .Col
                        Case 1
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000"
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                        Case 11
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "$###,###,###,##0.00"
                        Case 12
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos No Conciliados   -----
    pbarConciliacion.Value = 0
    If (fgNoConciliados.Rows = 1) Then
        pbarConciliacion.Max = 1
    Else
        pbarConciliacion.Max = fgNoConciliados.Rows - 1
    End If
    
    pbarConciliacion.Visible = True
    
    With fgNoConciliados
        Set HojaExcel = LibroExcel.Worksheets(3)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "No conciliados"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 3 de 4) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarConciliacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 3 de 4) pago no. 0 de 0  (100%)"
                pbarConciliacion.Value = 1
            End If
            
            For lContX = 1 To NUM_COLS_NOCONCILIADOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = &H404040     '----- Gris oscuro   -----
                    
                    Select Case .Col
                        Case 1
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000"
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                        Case 11
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "$###,###,###,##0.00"
                        Case 12
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 13
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 14
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos Distribuidos   -----
    pbarConciliacion.Value = 0
    If (fgDistribuidos.Rows = 1) Then
        pbarConciliacion.Max = 1
    Else
        pbarConciliacion.Max = fgDistribuidos.Rows - 1
    End If
    pbarConciliacion.Visible = True
    
    With fgDistribuidos
        Set HojaExcel = LibroExcel.Worksheets(4)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "Distribuidos"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 4 de 4) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarConciliacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando a Excel (Hoja 4 de 4) pago no. 0 de 0  (100%)"
                pbarConciliacion.Value = 1
            End If
            
            For lContX = 1 To NUM_COLS_DISTRIBUIDOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = &H404040     '----- Gris oscuro   -----
                    
                    Select Case .Col
                        Case 1
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000"
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                        Case 11
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "$###,###,###,##0.00"
                        Case 12
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    If (existe <> "") Then
        LibroExcel.Save
    Else
        'LibroExcel.SaveAs cdlgImportacion.FileName, , "miguelpm", "miguelpm"
        LibroExcel.SaveAs cdlgConciliacion.FileName
    End If
    LibroExcel.Close
    
    MsgBox "La exportación a Excel se ha realizado en forma satisfactoria.", vbOKOnly + vbInformation, TITULO_MENSAJE
    pbarConciliacion.Max = 1
    pbarConciliacion.Value = 0
    sbBarraEstado.Panels(1).Text = "Módulo de conciliación de pagos"
    pbarConciliacion.Value = 0
    cmdExpExcel.Enabled = True
    ctlFiltroConciliacion1.Habilitado = True
    cmdCerrar.Enabled = True
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cmdQuitarSel_Click()
    Dim iCont As Integer
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgPorConciliar
        .Col = 1
        For iCont = 1 To .Rows - 1
            .Row = iCont
            Set .CellPicture = pbSelNo.Picture
        Next
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cmdSelTodos_Click()
    Dim iCont As Integer
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgPorConciliar
        .Col = 1
        For iCont = 1 To .Rows - 1
            .Row = iCont
            Set .CellPicture = pbSel.Picture
        Next
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub BuscarPagos(ByVal psEmpresa As String, ByVal psFechaPago As String, ByVal psTipoCliente As String, ByVal psCodigo As String, ByVal psNombre As String, ByVal psCtaBancaria As String)
    Dim oRstConciliar As New clsoAdoRecordset
    Dim sCadenaSQL As String
    Dim sCondEmpresa As String, sCondFechaPago As String, sCondTipoCliente As String, sCondCodigo As String, sCondNombre1 As String, sCondNombre2 As String, sCondCtaBancaria As String
     
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With ctlFiltroConciliacion1
        If (.Empresa = "(Todas)") Then sCondEmpresa = " AND a.CDGEM = 'EMPFIN' " Else sCondEmpresa = "and      a.cdgem = '" & .Empresa & "' " & vbNewLine
        If Not (.OptFechaPago) Then sCondFechaPago = "" Else sCondFechaPago = "and      a.frealdep = '" & Format(.FechaPago, "yyyy/mm/dd") & "' " & vbNewLine
        If (.TipoCliente = "(Todos)") Then sCondTipoCliente = "" Else sCondTipoCliente = "and      a.clns = '" & Mid(.TipoCliente, 1, 1) & "' " & vbNewLine
        If (.Codigo = "") Then sCondCodigo = "" Else sCondCodigo = "and      a.cdgclns = '" & .Codigo & "' " & vbNewLine
        If (.Nombre = "") Then
            sCondNombre1 = ""
            sCondNombre2 = ""
        Else
            sCondNombre1 = "and      c.ciclo = '" & UCase(.Nombre) & "' " & vbNewLine
            sCondNombre2 = "and      c.ciclo = '" & UCase(.Nombre) & "' " & vbNewLine
        End If
        If (.CtaBancaria = "") Then sCondCtaBancaria = "" Else sCondCtaBancaria = "and      a.cdgcb = '" & .CtaBancaria & "' " & vbNewLine
    End With
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select    /*+RULE*/ a.cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgclns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.clns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          decode(a.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQL = sCadenaSQL & "                          'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgcl, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.ciclo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.periodo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          c.tasa, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          c.plazo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          c.periodicidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.secuencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.referencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.tipo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.modo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.conciliado, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.estatus, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.actualizarpe, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.secuenciaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.fechaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.periodo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          nvl(rtrim(ltrim(b.nombre1 || ' ' || b.nombre2)), '') || ' ' || nvl(rtrim(ltrim(b.primape || ' ' || b.segape)), '') nombre " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from      mp  a " & vbNewLine
    sCadenaSQL = sCadenaSQL & "left join cl  b " & vbNewLine
    sCadenaSQL = sCadenaSQL & "on        b.cdgem      = a.cdgem " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       b.codigo     = a.cdgclns " & vbNewLine
    sCadenaSQL = sCadenaSQL & "left join prc c " & vbNewLine
    sCadenaSQL = sCadenaSQL & "on        a.cdgem      = c.cdgem " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.cdgclns    = c.cdgcl " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.ciclo      = c.ciclo " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.clns       = c.clns " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where     a.conciliado IN ('N','C') " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.estatus    = 'B' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.clns       = 'I' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "AND fnRegresaSdoIndividual(a.CDGEM,a.CDGCLNS,a.CICLO) > 0 " & vbNewLine
    sCadenaSQL = sCadenaSQL & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre2 & sCondCtaBancaria
    sCadenaSQL = sCadenaSQL & "union all " & vbNewLine
    sCadenaSQL = sCadenaSQL & "select    /*+RULE*/ a.cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgclns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.clns, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          decode(a.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQL = sCadenaSQL & "                          'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgcl, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.ciclo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.periodo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          c.tasa, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          c.plazo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          c.periodicidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.secuencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.referencia, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.tipo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.cantidad, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.modo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.conciliado, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.estatus, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.actualizarpe, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.secuenciaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.fechaim, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          a.periodo, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "          rtrim(ltrim(nombre)) nombre " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from      mp  a " & vbNewLine
    sCadenaSQL = sCadenaSQL & "left join ns  b " & vbNewLine
    sCadenaSQL = sCadenaSQL & "on        b.cdgem       = a.cdgem " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       b.codigo      = a.cdgclns " & vbNewLine
    sCadenaSQL = sCadenaSQL & "left join prn c " & vbNewLine
    sCadenaSQL = sCadenaSQL & "on        a.cdgem      = c.cdgem " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.cdgclns    = c.cdgns " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.ciclo      = c.ciclo " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.clns       = 'G' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where     a.conciliado IN ('N','C') " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.estatus    = 'B' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and       a.clns       = 'G' " & vbNewLine
    'sCadenaSQL = sCadenaSQL & "AND FNREGRESASDOGRUPO(a.CDGEM,a.CDGCLNS,a.CICLO) > 0 " & vbNewLine
    sCadenaSQL = sCadenaSQL & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre1 & sCondCtaBancaria
    'sCadenaSQL = sCadenaSQL & sCondEmpresa & sCondTipoCliente & sCondCodigo & sCondNombre1 & sCondCtaBancaria
    'sCadenaSQL = sCadenaSQL & "and       a.cdgclns = '005011' "
    sCadenaSQL = sCadenaSQL & "order by  frealdep, cdgclns, ciclo, secuencia"  'AMGM 09JUL2010
    
    oRstConciliar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstConciliar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            MsgBox "No hay pagos pendientes de conciliar...", vbInformation + vbOKOnly, TITULO_MENSAJE
            oRstConciliar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con los pagos pendientes de conciliar   -----
            pbarConciliacion.Value = 0
            pbarConciliacion.Max = oRstConciliar.NumeroRegistros
            pbarConciliacion.Visible = True
            
            While Not oRstConciliar.FinDeArchivo
                pbarConciliacion.Value = Val(lbDatoNoRegsTab1.Caption)
                sbBarraEstado.Panels(1).Text = "Obteniendo pago no. " & CStr(lbDatoNoRegsTab1.Caption + 1) & " de " & CStr(oRstConciliar.NumeroRegistros) & "  (" & CStr(Format(((lbDatoNoRegsTab1.Caption + 1) * 100) / oRstConciliar.NumeroRegistros, "##0.00")) & "%)"
                Call PonerDatosPorConciliar(oRstConciliar)
                oRstConciliar.IrAlRegSiguiente
            Wend
            
            Screen.MousePointer = vbDefault
            MsgBox "Se encontraron un total de " & lbDatoNoRegsTab1.Caption & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la lista de pagos por conciliar..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConciliar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ctlFiltroConciliacion1_ClickBuscar()
    Dim oRstConciliar As New clsoAdoRecordset
    Dim sCadenaSQL As String

    On Error GoTo RutinaError

    
    If (Trim(ctlFiltroConciliacion1.Codigo) = "") And (Trim(ctlFiltroConciliacion1.Nombre) = "") And Not (ctlFiltroConciliacion1.OptFechaPago) Then
        MsgBox "Debe seleccionar al menos uno de los siguientes filtros:" & vbNewLine & " 1)Fecha " & vbNewLine & "2)Codigo y Ciclo" & vbNewLine & vbNewLine & "O falta llenar la información de los filtros seleccionados ", vbCritical + vbOKOnly, TITULO_MENSAJE
        Exit Sub
    End If
    
    Screen.MousePointer = vbHourglass
    
    dNoRegs = 0
    dMonto = 0
    sstConciliacion.Tab = 0
    Call BorrarFilasGrids
    lbDatoNoRegsTab1.Caption = "0"
    lbMontoTab1.Caption = "$0.00"

    With ctlFiltroConciliacion1
        cmdCerrar.Enabled = False
        cmdExpExcel.Visible = False
        cmdConciliacion.Visible = False
        cmdSelTodos.Visible = False
        cmdQuitarSel.Visible = False
        ctlFiltroConciliacion1.Habilitado = False
    
        Call BuscarPagos(.Empresa, .FechaPago, .TipoCliente, .Codigo, .Nombre, .CtaBancaria)
        
        pbarConciliacion.Value = 0
        pbarConciliacion.Visible = False
        sbBarraEstado.Panels(1).Text = "Se encontraron " & lbDatoNoRegsTab1.Caption & " pagos..."
        cmdSelTodos.Visible = True
        cmdQuitarSel.Visible = True
        cmdConciliacion.Visible = True
        cmdCerrar.Enabled = True
        ctlFiltroConciliacion1.Habilitado = True
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ctlFiltroConciliacion1_ClickCodigo()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
        ctlFiltroConciliacion1.OptCodigo = True
        ctlFiltroConciliacion1.OptNombre = True
End Sub

Private Sub ctlFiltroConciliacion1_ClickEmpresa()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptEmpresa = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickFechaPago()
    'If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_CON) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_CON
    'ctlFiltroConciliacion1.OptFechaPago = True
End Sub

Private Sub ctlFiltroConciliacion1_ClickNombre()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptNombre = True
    ctlFiltroConciliacion1.OptCodigo = True
End Sub

Private Sub fgPorConciliar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If (fgPorConciliar.Col = 1) Then
        If (fgPorConciliar.CellPicture = pbSel.Picture) Then
            Set fgPorConciliar.CellPicture = pbSelNo.Picture
        Else
            Set fgPorConciliar.CellPicture = pbSel.Picture
        End If
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    bCerrarForm = False
    sstConciliacion.Tab = 0
    Call InicializarGrids
    sbBarraEstado.Panels(1).Text = TITULO_MOD_CON
    ctlFiltroConciliacion1.OptFechaPago = True
    ctlFiltroConciliacion1.OptEmpresa = False
    ctlFiltroConciliacion1.QuitarFiltro = True
    cmdConciliacion.Visible = False

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerDatosPorConciliar(ByVal poRst As clsoAdoRecordset)
    Dim sFechaCarga As String, vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    sFechaCarga = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
    With Me.fgPorConciliar
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
        .CellPictureAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        Set .CellPicture = pbSel.Picture
        .Col = 2
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = sFechaCarga
        .Col = 3
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
        .Col = 4
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("frealdep").Value), "", poRst.ObjSetRegistros.Fields("frealdep").Value)
        .Col = 5
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
        .Col = 6
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("tipocte").Value), "", poRst.ObjSetRegistros.Fields("tipocte").Value)
        .Col = 7
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgclns").Value), "", poRst.ObjSetRegistros.Fields("cdgclns").Value)
        .Col = 8
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("ciclo").Value), "", poRst.ObjSetRegistros.Fields("ciclo").Value)
        .Col = 9
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("periodo").Value), "", poRst.ObjSetRegistros.Fields("periodo").Value)
        .Col = 10
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuenciaim").Value), "", poRst.ObjSetRegistros.Fields("secuenciaim").Value)
        .Col = 11
        .CellAlignment = flexAlignLeftCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("nombre").Value), "", poRst.ObjSetRegistros.Fields("nombre").Value)
        .Col = 12
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "", Format(poRst.ObjSetRegistros.Fields("cantidad").Value, "$###,###,###,##0.00"))
        .Col = 13
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
        .Col = 14
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgns").Value), "", poRst.ObjSetRegistros.Fields("cdgns").Value)
        .Col = 15
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("tasa").Value), "", poRst.ObjSetRegistros.Fields("tasa").Value)
        .Col = 16
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuencia").Value), "", poRst.ObjSetRegistros.Fields("secuencia").Value)
        .Col = 17
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("plazo").Value), "", poRst.ObjSetRegistros.Fields("plazo").Value)
        .Col = 18
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("periodicidad").Value), "", poRst.ObjSetRegistros.Fields("periodicidad").Value)
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

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    pbarConciliacion.Width = sbBarraEstado.Panels(2).Width - 40
    pbarConciliacion.Top = sbBarraEstado.Top + 60
    pbarConciliacion.Left = sbBarraEstado.Panels(1).Width + 80
    pbarConciliacion.Height = sbBarraEstado.Height - 100
    
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

Private Sub BorrarFilasGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    fgPorConciliar.Rows = 1
    fgPorConciliar.Refresh
    
    fgConciliados.Rows = 1
    fgConciliados.Refresh
    
    fgNoConciliados.Rows = 1
    fgNoConciliados.Refresh
    
    fgDistribuidos.Rows = 1
    fgDistribuidos.Refresh
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerDatosGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub InicializarGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    '-----   Inicializamos el Grid para los pagos por conciliar   -----
    With fgPorConciliar
        .Rows = 1
        .Cols = 19
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 600
        
        .Col = 1
        .TextMatrix(0, 1) = "Conciliar"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 800
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Carga"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 2100
        
        .Col = 3
        .TextMatrix(0, 3) = "Empresa"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 800
        
        .Col = 4
        .TextMatrix(0, 4) = "Fecha de Pago"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1200
        
        .Col = 5
        .TextMatrix(0, 5) = "Referencia"
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1100
        
        .Col = 6
        .TextMatrix(0, 6) = "Tipo Cte."
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1000
        
        .Col = 7
        .TextMatrix(0, 7) = "Código (Ind./Gpo.)"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 1500
        
        .Col = 8
        .TextMatrix(0, 8) = "Ciclo"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 500
        
        .Col = 9
        .TextMatrix(0, 9) = "Periodo"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 900
        
        .Col = 10
        .TextMatrix(0, 10) = "SecuenciaIM"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 1200
        
        .Col = 11
        .TextMatrix(0, 11) = "Nombre (Ind./Gpo.)"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(11) = 3300
        
        .Col = 12
        .TextMatrix(0, 12) = "Monto"
        .ColAlignment(12) = flexAlignCenterCenter
        .ColWidth(12) = 1100
        
        .Col = 13
        .TextMatrix(0, 13) = "Cta. Bancaria"
        .ColAlignment(13) = flexAlignCenterCenter
        .ColWidth(13) = 1200
        
        .Col = 14
        .TextMatrix(0, 14) = "Código Gpo."
        .ColAlignment(14) = flexAlignCenterCenter
        .ColWidth(14) = 1200
        
        .Col = 15
        .TextMatrix(0, 15) = "Tasa"
        .ColAlignment(15) = flexAlignCenterCenter
        .ColWidth(15) = 600
        
        .Col = 16
        .TextMatrix(0, 16) = "SecuenciaMP"
        .ColAlignment(16) = flexAlignCenterCenter
        .ColWidth(16) = 1200
        
        .Col = 17
        .TextMatrix(0, 17) = "Plazo"
        .ColAlignment(17) = flexAlignCenterCenter
        .ColWidth(17) = 800
        
        .Col = 18
        .TextMatrix(0, 18) = "Periodicidad"
        .ColAlignment(18) = flexAlignCenterCenter
        .ColWidth(18) = 1200
    End With
    '-----   Inicializamos el Grid para los pagos conciliados   -----
    With fgConciliados
        .Rows = 1
        .Cols = 15
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 600
        
        .Col = 1
        .TextMatrix(0, 1) = "Fecha de Carga"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 2100
        
        .Col = 2
        .TextMatrix(0, 2) = "Empresa"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 800
        
        .Col = 3
        .TextMatrix(0, 3) = "Fecha de Pago"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Referencia"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1100
        
        .Col = 5
        .TextMatrix(0, 5) = "Tipo Cte."
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1000
        
        .Col = 6
        .TextMatrix(0, 6) = "Código (Ind./Gpo.)"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1500
        
        .Col = 7
        .TextMatrix(0, 7) = "Ciclo"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 500
        
        .Col = 8
        .TextMatrix(0, 8) = "Periodo"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 900
        
        .Col = 9
        .TextMatrix(0, 9) = "SecuenciaIM"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 1200
        
        .Col = 10
        .TextMatrix(0, 10) = "Nombre (Ind./Gpo.)"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 3300
        
        .Col = 11
        .TextMatrix(0, 11) = "Monto"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(11) = 1100
        
        .Col = 12
        .TextMatrix(0, 12) = "Cta. Bancaria"
        .ColAlignment(12) = flexAlignCenterCenter
        .ColWidth(12) = 1200
        
        .Col = 13
        .TextMatrix(0, 13) = "SecuenciaMP"
        .ColAlignment(13) = flexAlignCenterCenter
        .ColWidth(13) = 1200
        
        .Col = 14
        .TextMatrix(0, 14) = "Observaciones"
        .ColAlignment(14) = flexAlignCenterCenter
        .ColWidth(14) = 5000
    End With
    '-----   Inicializamos el Grid para los pagos no conciliados   -----
    With fgNoConciliados
        .Rows = 1
        .Cols = 15
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 600
        
        .Col = 1
        .TextMatrix(0, 1) = "Fecha de Carga"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 2100
        
        .Col = 2
        .TextMatrix(0, 2) = "Empresa"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 800
        
        .Col = 3
        .TextMatrix(0, 3) = "Fecha de Pago"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Referencia"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1100
        
        .Col = 5
        .TextMatrix(0, 5) = "Tipo Cte."
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1000
        
        .Col = 6
        .TextMatrix(0, 6) = "Código (Ind./Gpo.)"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1500
        
        .Col = 7
        .TextMatrix(0, 7) = "Ciclo"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 500
        
        .Col = 8
        .TextMatrix(0, 8) = "Periodo"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 900
        
        .Col = 9
        .TextMatrix(0, 9) = "SecuenciaIM"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 1200
        
        .Col = 10
        .TextMatrix(0, 10) = "Nombre (Ind./Gpo.)"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 3300
        
        .Col = 11
        .TextMatrix(0, 11) = "Monto"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(11) = 1100
        
        .Col = 12
        .TextMatrix(0, 12) = "Cta. Bancaria"
        .ColAlignment(12) = flexAlignCenterCenter
        .ColWidth(12) = 1200
        
        .Col = 13
        .TextMatrix(0, 13) = "SecuenciaMP"
        .ColAlignment(13) = flexAlignCenterCenter
        .ColWidth(13) = 1200
        
        .Col = 14
        .TextMatrix(0, 14) = "Observaciones"
        .ColAlignment(14) = flexAlignCenterCenter
        .ColWidth(14) = 5000
    End With
    '-----   Inicializamos el Grid para los pagos distribuidos   -----
    With fgDistribuidos
        .Rows = 1
        .Cols = 13
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 600
        
        .Col = 1
        .TextMatrix(0, 1) = "Fecha de Carga"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 2100
        
        .Col = 2
        .TextMatrix(0, 2) = "Empresa"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 800
        
        .Col = 3
        .TextMatrix(0, 3) = "Fecha de Pago"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Referencia"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1100
        
        .Col = 5
        .TextMatrix(0, 5) = "Tipo Cte."
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1000
        
        .Col = 6
        .TextMatrix(0, 6) = "Código (Ind./Gpo.)"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1500
        
        .Col = 7
        .TextMatrix(0, 7) = "Ciclo"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 500
        
        .Col = 8
        .TextMatrix(0, 8) = "Periodo"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 900
        
        .Col = 9
        .TextMatrix(0, 9) = "SecuenciaMP"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 1200
        
        .Col = 10
        .TextMatrix(0, 10) = "Nombre (Ind./Gpo.)"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 3300
        
        .Col = 11
        .TextMatrix(0, 11) = "Monto"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(11) = 1100
        
        .Col = 12
        .TextMatrix(0, 12) = "Cta. Bancaria"
        .ColAlignment(12) = flexAlignCenterCenter
        .ColWidth(12) = 1200
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub
