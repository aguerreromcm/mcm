VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{BDC217C8-ED16-11CD-956C-0000C04E4C0A}#1.1#0"; "TABCTL32.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmImportacion 
   AutoRedraw      =   -1  'True
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Importación de Pagos"
   ClientHeight    =   9825
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9450
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmImportacion.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   9825
   ScaleWidth      =   9450
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9450
      TabIndex        =   50
      Top             =   0
      Width           =   9450
      Begin VB.PictureBox Picture1 
         Height          =   735
         Left            =   360
         Picture         =   "frmImportacion.frx":0442
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   57
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
         Left            =   9090
         TabIndex        =   53
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
         Left            =   7920
         TabIndex        =   52
         Top             =   180
         Width           =   1170
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Módulo de Importación de Pagos"
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
         TabIndex        =   51
         Top             =   60
         Width           =   4740
      End
   End
   Begin VB.PictureBox pbHSBC 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   210
      Left            =   3210
      Picture         =   "frmImportacion.frx":0A17
      ScaleHeight     =   210
      ScaleWidth      =   300
      TabIndex        =   27
      Top             =   7110
      Visible         =   0   'False
      Width           =   300
   End
   Begin VB.PictureBox pbBanamex 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   210
      Left            =   2850
      Picture         =   "frmImportacion.frx":0DA1
      ScaleHeight     =   210
      ScaleWidth      =   300
      TabIndex        =   26
      Top             =   7110
      Visible         =   0   'False
      Width           =   300
   End
   Begin VB.PictureBox pbBancomer 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   210
      Left            =   2490
      Picture         =   "frmImportacion.frx":112B
      ScaleHeight     =   210
      ScaleWidth      =   300
      TabIndex        =   25
      Top             =   7110
      Visible         =   0   'False
      Width           =   300
   End
   Begin VB.PictureBox pbNoIdentificado 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   195
      Left            =   1320
      Picture         =   "frmImportacion.frx":14B5
      ScaleHeight     =   195
      ScaleWidth      =   180
      TabIndex        =   24
      Top             =   7110
      Visible         =   0   'False
      Width           =   180
   End
   Begin VB.PictureBox pbIdentificado 
      AutoSize        =   -1  'True
      BorderStyle     =   0  'None
      Height          =   180
      Left            =   1080
      Picture         =   "frmImportacion.frx":16CB
      ScaleHeight     =   180
      ScaleWidth      =   180
      TabIndex        =   23
      Top             =   7110
      Visible         =   0   'False
      Width           =   180
   End
   Begin MSComDlg.CommonDialog cdlgImportacion 
      Left            =   450
      Top             =   9090
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
   End
   Begin MSComctlLib.ProgressBar pbarImportacion 
      Height          =   195
      Left            =   4080
      TabIndex        =   11
      Top             =   9600
      Width           =   2415
      _ExtentX        =   4260
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   12
      Top             =   9540
      Width           =   9450
      _ExtentX        =   16669
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   7056
            MinWidth        =   7056
            Text            =   "Módulo de importación de pagos "
            TextSave        =   "Módulo de importación de pagos "
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
   Begin VB.PictureBox pbContImportacion 
      Align           =   1  'Align Top
      BackColor       =   &H00FFF9F9&
      Height          =   8805
      Left            =   0
      ScaleHeight     =   8745
      ScaleWidth      =   9390
      TabIndex        =   13
      Top             =   735
      Width           =   9450
      Begin VB.PictureBox pbPago 
         AutoSize        =   -1  'True
         BorderStyle     =   0  'None
         Height          =   210
         Left            =   2280
         Picture         =   "frmImportacion.frx":18BD
         ScaleHeight     =   210
         ScaleWidth      =   210
         TabIndex        =   55
         Top             =   8370
         Visible         =   0   'False
         Width           =   210
      End
      Begin VB.CommandButton cmdBuscar 
         Caption         =   "&Buscar"
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
         Left            =   8310
         TabIndex        =   54
         Top             =   2490
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
         Left            =   7650
         TabIndex        =   49
         Top             =   7920
         Visible         =   0   'False
         Width           =   1600
      End
      Begin VB.PictureBox pbNoValidado 
         AutoSize        =   -1  'True
         BorderStyle     =   0  'None
         Height          =   180
         Left            =   2040
         Picture         =   "frmImportacion.frx":1B67
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   38
         Top             =   8370
         Visible         =   0   'False
         Width           =   180
      End
      Begin VB.PictureBox pbNoImportado 
         AutoSize        =   -1  'True
         BorderStyle     =   0  'None
         Height          =   195
         Left            =   1560
         Picture         =   "frmImportacion.frx":1D59
         ScaleHeight     =   195
         ScaleWidth      =   180
         TabIndex        =   37
         Top             =   8370
         Visible         =   0   'False
         Width           =   180
      End
      Begin VB.PictureBox pbArqueoCaja 
         AutoSize        =   -1  'True
         BorderStyle     =   0  'None
         Height          =   195
         Left            =   1800
         Picture         =   "frmImportacion.frx":1F6F
         ScaleHeight     =   195
         ScaleWidth      =   180
         TabIndex        =   36
         Top             =   8370
         Visible         =   0   'False
         Width           =   180
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
         ItemData        =   "frmImportacion.frx":2185
         Left            =   90
         List            =   "frmImportacion.frx":2187
         Style           =   2  'Dropdown List
         TabIndex        =   4
         Top             =   930
         Width           =   9225
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
         ItemData        =   "frmImportacion.frx":2189
         Left            =   90
         List            =   "frmImportacion.frx":2190
         Style           =   2  'Dropdown List
         TabIndex        =   1
         Top             =   300
         Width           =   1395
      End
      Begin TabDlg.SSTab sstImportacion 
         Height          =   5175
         Left            =   60
         TabIndex        =   8
         Top             =   3120
         Width           =   9300
         _ExtentX        =   16404
         _ExtentY        =   9128
         _Version        =   393216
         Tabs            =   5
         TabsPerRow      =   5
         TabHeight       =   520
         BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         TabCaption(0)   =   "Excel"
         TabPicture(0)   =   "frmImportacion.frx":219C
         Tab(0).ControlEnabled=   -1  'True
         Tab(0).Control(0)=   "lbRegsTab1"
         Tab(0).Control(0).Enabled=   0   'False
         Tab(0).Control(1)=   "lbDatoNoRegsTab1"
         Tab(0).Control(1).Enabled=   0   'False
         Tab(0).Control(2)=   "lbMontoTab1"
         Tab(0).Control(2).Enabled=   0   'False
         Tab(0).Control(3)=   "Label13"
         Tab(0).Control(3).Enabled=   0   'False
         Tab(0).Control(4)=   "fgImportacion"
         Tab(0).Control(4).Enabled=   0   'False
         Tab(0).ControlCount=   5
         TabCaption(1)   =   "Identificados"
         TabPicture(1)   =   "frmImportacion.frx":21B8
         Tab(1).ControlEnabled=   0   'False
         Tab(1).Control(0)=   "fgIdentificados"
         Tab(1).Control(1)=   "Label11"
         Tab(1).Control(2)=   "lbMontoTab2"
         Tab(1).Control(3)=   "lbDatoNoRegsTab2"
         Tab(1).Control(4)=   "lbRegsTab2"
         Tab(1).ControlCount=   5
         TabCaption(2)   =   "Garantias"
         TabPicture(2)   =   "frmImportacion.frx":21D4
         Tab(2).ControlEnabled=   0   'False
         Tab(2).Control(0)=   "fgNoIdentificados"
         Tab(2).Control(1)=   "Label5"
         Tab(2).Control(2)=   "lbMontoTab3"
         Tab(2).Control(3)=   "lbDatoNoRegsTab3"
         Tab(2).Control(4)=   "lbRegsTab3"
         Tab(2).ControlCount=   5
         TabCaption(3)   =   "No importados"
         TabPicture(3)   =   "frmImportacion.frx":21F0
         Tab(3).ControlEnabled=   0   'False
         Tab(3).Control(0)=   "fgNoImportados"
         Tab(3).Control(1)=   "Label7"
         Tab(3).Control(2)=   "lbMontoTab4"
         Tab(3).Control(3)=   "lbRegsTab4"
         Tab(3).Control(4)=   "lbDatoNoRegsTab4"
         Tab(3).ControlCount=   5
         TabCaption(4)   =   "Arqueo de caja"
         TabPicture(4)   =   "frmImportacion.frx":220C
         Tab(4).ControlEnabled=   0   'False
         Tab(4).Control(0)=   "fgArqueoCaja"
         Tab(4).Control(1)=   "Label9"
         Tab(4).Control(2)=   "lbMontoTab5"
         Tab(4).Control(3)=   "lbRegsTab5"
         Tab(4).Control(4)=   "lbDatoNoRegsTab5"
         Tab(4).ControlCount=   5
         Begin MSFlexGridLib.MSFlexGrid fgNoIdentificados 
            Height          =   4400
            Left            =   -74940
            TabIndex        =   14
            Top             =   360
            Width           =   9200
            _ExtentX        =   16219
            _ExtentY        =   7752
            _Version        =   393216
            BackColor       =   16250879
            WordWrap        =   -1  'True
            HighLight       =   2
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
         Begin MSFlexGridLib.MSFlexGrid fgIdentificados 
            Height          =   4400
            Left            =   -74940
            TabIndex        =   15
            Top             =   360
            Width           =   9200
            _ExtentX        =   16219
            _ExtentY        =   7752
            _Version        =   393216
            BackColor       =   16252919
            HighLight       =   2
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
         Begin MSFlexGridLib.MSFlexGrid fgImportacion 
            Height          =   4400
            Left            =   60
            TabIndex        =   16
            Top             =   360
            Width           =   9195
            _ExtentX        =   16219
            _ExtentY        =   7752
            _Version        =   393216
            BackColor       =   16777215
            HighLight       =   2
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
         Begin MSFlexGridLib.MSFlexGrid fgNoImportados 
            Height          =   4400
            Left            =   -74940
            TabIndex        =   30
            Top             =   360
            Width           =   9200
            _ExtentX        =   16219
            _ExtentY        =   7752
            _Version        =   393216
            BackColor       =   16250879
            WordWrap        =   -1  'True
            HighLight       =   2
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
         Begin MSFlexGridLib.MSFlexGrid fgArqueoCaja 
            Height          =   4400
            Left            =   -74940
            TabIndex        =   31
            Top             =   360
            Width           =   9200
            _ExtentX        =   16219
            _ExtentY        =   7752
            _Version        =   393216
            BackColor       =   16250879
            WordWrap        =   -1  'True
            HighLight       =   2
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
            Left            =   3315
            TabIndex        =   48
            Top             =   4905
            Width           =   525
         End
         Begin VB.Label lbMontoTab1 
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
            Left            =   3915
            TabIndex        =   47
            Top             =   4905
            Width           =   120
         End
         Begin VB.Label Label11 
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
            Left            =   -71685
            TabIndex        =   46
            Top             =   4905
            Width           =   525
         End
         Begin VB.Label lbMontoTab2 
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
            ForeColor       =   &H00008000&
            Height          =   195
            Left            =   -71085
            TabIndex        =   45
            Top             =   4905
            Width           =   120
         End
         Begin VB.Label Label9 
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
            Left            =   -71685
            TabIndex        =   44
            Top             =   4905
            Width           =   525
         End
         Begin VB.Label lbMontoTab5 
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
            ForeColor       =   &H00000080&
            Height          =   195
            Left            =   -71085
            TabIndex        =   43
            Top             =   4905
            Width           =   120
         End
         Begin VB.Label Label7 
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
            Left            =   -71685
            TabIndex        =   42
            Top             =   4905
            Width           =   525
         End
         Begin VB.Label lbMontoTab4 
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
            Left            =   -71085
            TabIndex        =   41
            Top             =   4905
            Width           =   120
         End
         Begin VB.Label Label5 
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
            Left            =   -71685
            TabIndex        =   40
            Top             =   4905
            Width           =   525
         End
         Begin VB.Label lbMontoTab3 
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
            ForeColor       =   &H00C00000&
            Height          =   195
            Left            =   -71085
            TabIndex        =   39
            Top             =   4905
            Width           =   120
         End
         Begin VB.Label lbRegsTab5 
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
            Left            =   -74910
            TabIndex        =   35
            Top             =   4900
            Width           =   1260
         End
         Begin VB.Label lbDatoNoRegsTab5 
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
            ForeColor       =   &H00000080&
            Height          =   195
            Left            =   -73620
            TabIndex        =   34
            Top             =   4900
            Width           =   120
         End
         Begin VB.Label lbRegsTab4 
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
            Left            =   -74910
            TabIndex        =   33
            Top             =   4900
            Width           =   1260
         End
         Begin VB.Label lbDatoNoRegsTab4 
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
            Left            =   -73620
            TabIndex        =   32
            Top             =   4900
            Width           =   120
         End
         Begin VB.Label lbDatoNoRegsTab3 
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
            ForeColor       =   &H00C00000&
            Height          =   195
            Left            =   -73620
            TabIndex        =   22
            Top             =   4900
            Width           =   120
         End
         Begin VB.Label lbRegsTab3 
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
            Left            =   -74910
            TabIndex        =   21
            Top             =   4900
            Width           =   1260
         End
         Begin VB.Label lbDatoNoRegsTab2 
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
            ForeColor       =   &H00008000&
            Height          =   195
            Left            =   -73620
            TabIndex        =   20
            Top             =   4900
            Width           =   120
         End
         Begin VB.Label lbRegsTab2 
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
            Left            =   -74910
            TabIndex        =   19
            Top             =   4900
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
            Left            =   1380
            TabIndex        =   18
            Top             =   4900
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
            TabIndex        =   17
            Top             =   4900
            Width           =   1260
         End
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
         Left            =   8370
         TabIndex        =   10
         Top             =   8370
         Width           =   1000
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
         Left            =   6690
         TabIndex        =   9
         Top             =   8370
         Width           =   1600
      End
      Begin VB.CommandButton cmdArchivoOrigen 
         Caption         =   "..."
         Default         =   -1  'True
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   270
         Left            =   9000
         TabIndex        =   7
         Top             =   2100
         Width           =   300
      End
      Begin MSComCtl2.DTPicker DPFechaPago 
         Height          =   300
         Left            =   1650
         TabIndex        =   56
         Top             =   300
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
         Format          =   16646145
         CurrentDate     =   38597
      End
      Begin VB.Line Line2 
         BorderColor     =   &H00D0D0D0&
         BorderWidth     =   2
         X1              =   120
         X2              =   9300
         Y1              =   2970
         Y2              =   2970
      End
      Begin VB.Line Line1 
         BorderColor     =   &H00F0F0F0&
         BorderWidth     =   2
         X1              =   120
         X2              =   9300
         Y1              =   2940
         Y2              =   2940
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
         Left            =   1650
         TabIndex        =   2
         Top             =   90
         Width           =   1155
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
         Left            =   90
         TabIndex        =   29
         Top             =   1530
         Width           =   9225
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
         Left            =   90
         TabIndex        =   28
         Top             =   1320
         Width           =   960
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
         Left            =   90
         TabIndex        =   3
         Top             =   720
         Width           =   1305
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
         Left            =   90
         TabIndex        =   0
         Top             =   90
         Width           =   720
      End
      Begin VB.Label lbArchivo 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Archivo:"
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
         TabIndex        =   5
         Top             =   1890
         Width           =   645
      End
      Begin VB.Label lbArchivoOrigen 
         BackColor       =   &H00FFFFFF&
         BorderStyle     =   1  'Fixed Single
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   285
         Left            =   90
         TabIndex        =   6
         Top             =   2100
         Width           =   8895
      End
   End
End
Attribute VB_Name = "frmImportacion"
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

Private Const NUM_COLS_PROCESADOS = 10  'AMGM 2015 Se agrego la columna MONEDA por temas de PLD
Private Const NUM_COLS_IDENTIFICADOS = 11 'AMGM 2015 Se agrego la columna MONEDA por temas de PLD
Private Const NUM_COLS_NOIDENTIFICADOS = 11 'AMGM 2015 Se agrego la columna MONEDA por temas de PLD
Private Const NUM_COLS_NOIMPORTADOS = 11 'AMGM 2015 Se agrego la columna MONEDA por temas de PLD
Private Const NUM_COLS_ARQUEOCAJA = 10
Private Const NOMBRE_FONT = "Verdana"
Private Const TAMAÑO_FONT = 8

Private Sub cmdArchivoOrigen_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call BorrarFilasGrids
    cmdExpExcel.Visible = False
    cdlgImportacion.Filter = "Archivos de Excel (*.xls)|*.xls|Archivos de Texto (*.txt)|*.txt"
    Screen.MousePointer = vbDefault
    cdlgImportacion.ShowOpen
    Screen.MousePointer = vbHourglass
    lbArchivoOrigen.Caption = " " & cdlgImportacion.FileName
    sDocImpActual = cdlgImportacion.FileName
    cmdArchivoOrigen.Default = False
    cmdImportacion.Default = True
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cmdBuscar_Click()
    Dim res As Variant
    Dim sMensaje As String, smsgval As String
    Dim oAccesoDatosXLS As New clsoAccesoDatos
    Dim bFechaOK As Boolean, bReferenciaOK As Boolean, bMontoOK As Boolean
    Dim vColorFrente As Variant, vColorFondo As Variant
    Dim lPagosOK As Long
    Dim oRstPago As New clsoAdoRecordset
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If (lbArchivoOrigen.Caption = "") Then
        Screen.MousePointer = vbDefault
        MsgBox "Debe seleccionar el archivo a importar.", vbCritical + vbOKOnly, TITULO_MENSAJE
        Screen.MousePointer = vbHourglass
        cmdArchivoOrigen.SetFocus
    Else
        lbDatoNoRegsTab1 = "0"
        lbDatoNoRegsTab2 = "0"
        lbDatoNoRegsTab3 = "0"
        lbDatoNoRegsTab4 = "0"
        lbDatoNoRegsTab5 = "0"
        lbMontoTab1 = "$0.00"
        lbMontoTab2 = "$0.00"
        lbMontoTab3 = "$0.00"
        lbMontoTab4 = "$0.00"
        lbMontoTab5 = "$0.00"
        lPagosOK = 0
        
        'Call InicializarGrids
        cPagoImp.Preguntar = True
        sFechaCarga = Format(Date, "DD/MM/YYYY") & " " & Format(Time, "HH:NN:SS am/pm")
        
        sMensaje = ""
        sMensaje = sMensaje & "¿Esta seguro(a) que obtener los pagos del archivo de Excel para?" & vbNewLine & vbNewLine
        sMensaje = sMensaje & "Empresa:" & vbTab & vbTab & cbEmpresa.Text & vbNewLine
        sMensaje = sMensaje & "Cta. bancaria:" & vbTab & cbCuentaBancaria.Text & vbNewLine
        sMensaje = sMensaje & "Descripción:" & vbTab & Trim(lbBanco.Caption)
        Screen.MousePointer = vbDefault
        res = MsgBox(sMensaje, vbQuestion + vbYesNo, TITULO_MENSAJE)
        Screen.MousePointer = vbHourglass
        If (res = vbYes) Then
            BorrarFilasGrids
            sstImportacion.Tab = 0
            Call HabilitarControles(False)
            cmdImportacion.Visible = False

            'oAccesoDatosXLS.cnn.Proveedor = "Microsoft.Jet.OLEDB.4.0"
            oAccesoDatosXLS.cnn.Proveedor = "Microsoft.ACE.OLEDB.12.0"
            '-----   Si el archivo no tiene extenxión entonces se la agregamos y construimos la cadena de conexion   -----
            If (UCase(Mid(Trim(lbArchivoOrigen.Caption), Len(Trim(lbArchivoOrigen.Caption)) - 3, 4)) <> ".XLS") Then
                oAccesoDatosXLS.cnn.CadenaConexion = "Data Source=" & Trim(lbArchivoOrigen.Caption) & ".XLS"
                sNomArcExcel = Trim(lbArchivoOrigen.Caption) & ".XLS"
            Else
                oAccesoDatosXLS.cnn.CadenaConexion = "Data Source=" & Trim(lbArchivoOrigen.Caption)
                sNomArcExcel = Trim(lbArchivoOrigen.Caption)
            End If
            
            oAccesoDatosXLS.cnn.Propiedades("Extended Properties") = "Excel 12.0"
            oAccesoDatosXLS.cnn.Abrir
                        
            oAccesoDatosXLS.rst.Abrir "select * from [Hoja1$]", oAccesoDatosXLS.cnn.ObjConexion, adOpenKeyset, adLockOptimistic
            
            Select Case oAccesoDatosXLS.rst.HayRegistros
                Case 0  '-----   La consulta no retorno registros.    -----
                    Screen.MousePointer = vbDefault
                    MsgBox "La consulta al archivo de Excel no retorno registros." & vbNewLine & vbNewLine & "Verifique que el archivo no este vacío." & vbNewLine & vbNewLine & "Si continua con los problemas, consulte al administrador de la Aplicación", vbCritical + vbOKOnly, TITULO_MENSAJE
                    Screen.MousePointer = vbHourglass
                Case 1  '-----   Hay registros.                       -----
                    lNoRegsExcel = oAccesoDatosXLS.rst.NumeroRegistros
                    lContador = 0
                    pbarImportacion.Max = lNoRegsExcel
                    While Not oAccesoDatosXLS.rst.FinDeArchivo
                        lContador = lContador + 1
                        DoEvents
                        sbBarraEstado.Panels(1).Text = "Procesando " & CStr(lContador) & " de " & CStr(lNoRegsExcel) & " registros  (" & Format((lContador / lNoRegsExcel) * 100, "##0.00") & "%)"
                        pbarImportacion.Value = lContador
                    
                        DoEvents
                        With fgImportacion
                            .Rows = .Rows + 1
                            .Row = .Rows - 1
                            
                            bFechaOK = False
                            smsgval = ""
                                If (Format(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Fecha").Value, "DD/MM/YYYY") <> Format(Me.dpFechaPago.Value, "DD/MM/YYYY")) Or (oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Moneda").Value <> "MN") Then
                                'If (Format(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Fecha").Value, "DD/MM/YYYY") <> Format(Me.DPFechaPago.Value, "DD/MM/YYYY")) Then
                                    bFechaOK = False
                                Else
                                    bFechaOK = True
                                End If
                            If (Not bFechaOK) Then smsgval = "Fecha no válida"
                            If (bFechaOK) Then  'Esta linea se agrego
                                vColorFrente = vbBlack
                                vColorFondo = vbWhite
                            Else
                                vColorFrente = vbRed
                                vColorFondo = vbWhite
                            End If
                            
                            .Col = 0
                            .CellAlignment = flexAlignCenterCenter
                            .Text = CStr(.Row)
                            .Col = 1
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignCenterCenter
                            .CellPictureAlignment = flexAlignCenterCenter
                            If (bFechaOK) Then  'Estalinea se agrego
                                Set .CellPicture = pbPago.Picture
                            Else
                                Set .CellPicture = pbNoValidado.Picture
                            End If
                            
                            .Text = ""
                            .Col = 2
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignCenterCenter
                            .Text = Format(Date, "DD/MM/YYYY") & " " & Format(Time, "HH:NN:SS am/pm")
                            .Col = 3
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignCenterCenter
                            .Text = Mid(cbCuentaBancaria.Text, 1, 2)
                            .Col = 4
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignCenterCenter
                            .Text = IIf(IsNull(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Fecha").Value), "", oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Fecha").Value)
                            .Col = 5
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignCenterCenter
                            .Text = IIf(IsNull(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Referencia").Value), "", oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Referencia").Value)
                            .Col = 6
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignRightCenter
                            .Text = IIf(IsNull(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Monto").Value), "0", Format(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Monto").Value, "$###,###,###,###,##0.00"))
                            .Col = 10
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignRightCenter
                            .Text = IIf(IsNull(oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Moneda").Value), "", oAccesoDatosXLS.rst.ObjSetRegistros.Fields("Moneda").Value)
                            .Col = 8
                            .CellForeColor = vColorFrente
                            .CellBackColor = vColorFondo
                            .CellAlignment = flexAlignLeftCenter
                                .Text = "Por importar"
                            
                            DoEvents
                            lbDatoNoRegsTab1.Caption = CStr(CDbl(lbDatoNoRegsTab1.Caption) + 1)
                            lbMontoTab1.Caption = Format(CStr(CDbl(lbMontoTab1.Caption) + .TextMatrix(.Row, 6)), "$###,###,###,###,##0.00")
                        End With
                        
                        lPagosOK = lPagosOK + 1
                        oAccesoDatosXLS.rst.IrAlRegSiguiente
                    Wend
                    
                    MsgBox "Se obtuvieron del archivo de Excel un total de " & CStr(lNoRegsExcel) & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
                    sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
                    pbarImportacion.Value = 0
                    
                Case 2  '-----   El Query no se pudo ejecutar.        -----
                    Screen.MousePointer = vbDefault
                    MsgBox "No fue posible abrir la Conexión con el archivo: " & sNomArcExcel & vbNewLine & vbNewLine & "Intentelo nuevamente o consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                    Screen.MousePointer = vbHourglass
            End Select
            
            oAccesoDatosXLS.rst.Cerrar
            oAccesoDatosXLS.rst.LiberarRecurso

            cmdImportacion.Visible = True
            Call HabilitarControles(True)
            If (lPagosOK = 0) Then
                cmdImportacion.Enabled = False
            End If
        Else
            cbEmpresa.SetFocus
        End If
        
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

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

Private Sub cmdExpExcel_Click()
    Dim AppExcel As New Excel.Application
    Dim LibroExcel As New Excel.Workbook
    Dim HojaExcel As New Excel.Worksheet
    Dim lContX As Long, lContY As Long, sRango As String
    Dim sLetra As String, sNumero As String
    Dim existe As Variant, vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    cdlgImportacion.Filter = "Archivos de Excel (*.xls)|*.xls"
    cdlgImportacion.ShowSave
    
    existe = ""
    existe = Dir(cdlgImportacion.FileName)
    
    Set LibroExcel = AppExcel.Workbooks.Add(xlWBATWorksheet)
    LibroExcel.Worksheets.Add
    LibroExcel.Worksheets.Add
    LibroExcel.Worksheets.Add
    LibroExcel.Worksheets.Add
    
    '-----   Incluimos los pagos procesados en el archivo de Excel   -----
    With fgImportacion
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
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 1/5) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarImportacion.Max = .Rows - 1
                pbarImportacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 1/5) pago no. 0 de 0  (100%)"
                pbarImportacion.Max = 1
                pbarImportacion.Value = 1
            End If
            
            If (.Row Mod 2 = 1) Then
                vColorFrente = &H808080
                vColorFondo = vbWhite
            Else
                vColorFrente = &H808080
                vColorFondo = vbWhite
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
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vColorFrente
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = vColorFondo
                    
                    Select Case .Col
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            If (.Row = 0) Then HojaExcel.Columns("A:Z").AutoFit
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos Identificados   -----
    With fgIdentificados
        Set HojaExcel = LibroExcel.Worksheets(2)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "Identificados"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 2/5) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarImportacion.Max = .Rows - 1
                pbarImportacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 2/5) pago no. 0 de 0  (100%)"
                pbarImportacion.Max = 1
                pbarImportacion.Value = 1
            End If
            
            If (.Row Mod 2 = 1) Then
                vColorFrente = &H808080
                vColorFondo = vbWhite
            Else
                vColorFrente = &H808080
                vColorFondo = &HF7FFF7
            End If
            
            For lContX = 1 To NUM_COLS_IDENTIFICADOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vColorFrente
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = vColorFondo
                    
                    Select Case .Col
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos No Identificados   -----
    With fgNoIdentificados
        Set HojaExcel = LibroExcel.Worksheets(3)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "Garantias"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 3/5) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarImportacion.Max = .Rows - 1
                pbarImportacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 3/5) pago no. 0 de 0  (100%)"
                pbarImportacion.Max = 1
                pbarImportacion.Value = 1
            End If
            
            If (.Row Mod 2 = 1) Then
                vColorFrente = &H808080
                vColorFondo = vbWhite
            Else
                vColorFrente = &H808080
                vColorFondo = &HF7F7FF
            End If
            
            For lContX = 1 To NUM_COLS_NOIDENTIFICADOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vColorFrente
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = vColorFondo
                    
                    Select Case .Col
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos No Importados   -----
    With fgNoImportados
        Set HojaExcel = LibroExcel.Worksheets(4)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "No Importados"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 4/5) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarImportacion.Max = .Rows - 1
                pbarImportacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 4/5) pago no. 0 de 0  (100%)"
                pbarImportacion.Max = 1
                pbarImportacion.Value = 1
            End If
            
            If (.Row Mod 2 = 1) Then
                vColorFrente = &HFF
                vColorFondo = vbWhite
            Else
                vColorFrente = &HFF
                vColorFondo = vbWhite
            End If
            
            For lContX = 1 To NUM_COLS_NOIMPORTADOS
                .Col = lContX
                sLetra = Chr(Asc(sLetra) + 1)
                sNumero = CStr(.Row + 1)
                If (.Row = 0) Then
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vbWhite
                    HojaExcel.Range(sLetra & sNumero).Font.Bold = True
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = &H8000000F
                    HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                Else
                    HojaExcel.Range(sLetra & sNumero).Font.Color = vColorFrente
                    HojaExcel.Range(sLetra & sNumero).Interior.Color = vColorFondo
                    
                    Select Case .Col
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
                    End Select
                End If
                
                HojaExcel.Range(sLetra & sNumero).Value = CStr(.TextMatrix(.Row, .Col))
                HojaExcel.Columns("A:Z").AutoFit
            Next
            sLetra = Chr(64)
        Next
    End With
    
    '-----   Incluimos los pagos de Arqueo de Caja   -----
    With fgArqueoCaja
        Set HojaExcel = LibroExcel.Worksheets(5)
        HojaExcel.Cells.Font.Name = NOMBRE_FONT
        HojaExcel.Cells.Font.Size = TAMAÑO_FONT
        HojaExcel.Name = "Arqueo de Caja"
        sLetra = Chr(64)    '-----   Iniciamos con la columna A (Iniciamos con 63 porque este valor se incrementará en el FOR)   -----
        'HojaExcel.Columns(Chr(65) & ":" & Chr(65 + NUM_COLS_PROCESADOS)).AutoFit
        HojaExcel.Columns("A:Z").AutoFit
        
        For lContY = 0 To .Rows - 1
            .Row = lContY
            
            If ((.Rows - 1) > 0) Then
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 5/5) pago no. " & CStr(lContY) & " de " & CStr(.Rows - 1) & "  (" & Format(CStr(((lContY) * 100) / (.Rows - 1)), "##0.00") & "%)"
                pbarImportacion.Max = .Rows - 1
                pbarImportacion.Value = lContY
            Else
                sbBarraEstado.Panels(1).Text = "Exportando (Hoja 5/5) pago no. 0 de 0  (100%)"
                pbarImportacion.Max = 1
                pbarImportacion.Value = 1
            End If
            
            For lContX = 1 To 10   'NUM_COLS_ARQUEOCAJA
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
                    'HojaExcel.Range(sLetra & sNumero).Borders = True
                    
                    Select Case .Col
                        Case 2
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 3
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "00"
                        Case 4
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 5
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                            HojaExcel.Range(sLetra & sNumero).NumberFormat = "000000000"
                        Case 6
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlRight
                        Case 7
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 8
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 9
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlCenter
                        Case 10
                            HojaExcel.Range(sLetra & sNumero).HorizontalAlignment = xlLeft
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
        LibroExcel.SaveAs cdlgImportacion.FileName
    End If
    LibroExcel.Close
    
    MsgBox "La exportación a Excel se ha realizado en forma satisfactoria.", vbOKOnly + vbInformation, TITULO_MENSAJE
    pbarImportacion.Max = 1
    pbarImportacion.Value = 0
    sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
    
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

    If (lbArchivoOrigen.Caption = "") Then
        Screen.MousePointer = vbDefault
        MsgBox "Debe seleccionar el archivo a importar.", vbCritical + vbOKOnly, TITULO_MENSAJE
        Screen.MousePointer = vbHourglass
        cmdArchivoOrigen.SetFocus
    Else
        cPagoImp.Preguntar = True
        sFechaCarga = Format(Date, "YYYY/MM/DD")
        sMensaje = ""
        sMensaje = sMensaje & "¿Esta seguro(a) que desea importar los pagos para?" & vbNewLine & vbNewLine
        sMensaje = sMensaje & "Empresa:" & vbTab & vbTab & cbEmpresa.Text & vbNewLine
        sMensaje = sMensaje & "Cta. bancaria:" & vbTab & cbCuentaBancaria.Text & vbNewLine
        sMensaje = sMensaje & "Descripción:" & vbTab & Trim(lbBanco.Caption)
        Screen.MousePointer = vbDefault
        res = MsgBox(sMensaje, vbQuestion + vbYesNo, TITULO_MENSAJE)
        Screen.MousePointer = vbHourglass
        If (res = vbYes) Then
            'BorrarFilasGrids
            sstImportacion.Tab = 0
            Call HabilitarControles(False)
            Call ImportacionTotales
            Call EjecutarImportacion
            Call ImportacionTotalesDet
            Call HabilitarControles(True)
            cmdExpExcel.Visible = True
            cmdImportacion.Enabled = False
        Else
            cbEmpresa.SetFocus
        End If
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub HabilitarControles(ByVal pbEstado As Boolean)
    If (pbEstado) Then
        cmdArchivoOrigen.Enabled = True
        cmdCerrar.Enabled = True
        cmdImportacion.Enabled = True
        cbCuentaBancaria.Enabled = True
        cbEmpresa.Enabled = True
        sstImportacion.Enabled = True
        cmdBuscar.Enabled = True
        dpFechaPago.Enabled = True
    Else
        cmdArchivoOrigen.Enabled = False
        cmdCerrar.Enabled = False
        cmdImportacion.Enabled = False
        cbCuentaBancaria.Enabled = False
        cbEmpresa.Enabled = False
        sstImportacion.Enabled = False
        cmdBuscar.Enabled = False
        dpFechaPago.Enabled = False
    End If
End Sub

Private Sub fgImportacion_DblClick()
    With fgImportacion
        .Col = 1
        Select Case .CellPicture
            Case pbIdentificado.Picture
                .Col = 8
                sstImportacion.Tab = 1
                fgIdentificados.Row = Val(.Text)
                fgIdentificados.RowSel = Val(.Text)
                fgIdentificados.Col = 0
                fgIdentificados.ColSel = 6
                fgIdentificados.SetFocus
            Case pbNoIdentificado.Picture
                .Col = 9
                sstImportacion.Tab = 2
                fgNoIdentificados.Row = Val(.Text)
                fgNoIdentificados.RowSel = Val(.Text)
                fgNoIdentificados.Col = 0
                fgNoIdentificados.ColSel = 7
                fgNoIdentificados.SetFocus
            Case pbNoImportado.Picture
                .Col = 10
                sstImportacion.Tab = 3
                fgNoImportados.Row = Val(.Text)
                fgNoImportados.RowSel = Val(.Text)
                fgNoImportados.Col = 0
                fgNoImportados.ColSel = 6
                fgNoImportados.SetFocus
            Case Else
                '-----   No hacemos nada   -----
        End Select
    End With
End Sub


Private Sub Form_Load()
    Dim oRstInfo As New clsoAdoRecordset
    Dim sCadenaSQL As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    '--- CAMBIO POR DIEGO TRINADO QUE PERMITE OBTENER LA FECHA DE LA BD
    sCadenaSQL = ""
    sCadenaSQL = "select NVL(FNFECHAFINHABIL(TRUNC(SYSDATE)),TRUNC(SYSDATE)) fecha "
    sCadenaSQL = sCadenaSQL & " ,FNFECHAANTERIOR(TRUNC(SYSDATE)) fechaMin "
    sCadenaSQL = sCadenaSQL & " from dual "
    
    If (oRstInfo.Estado = adStateOpen) Then oRstInfo.Cerrar
        oRstInfo.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
        
    Select Case oRstInfo.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la información solicitada." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstInfo.Cerrar
            Screen.MousePointer = vbDefault
            Exit Sub
        Case 1  '-----   Hay registros.                       -----
            dpFechaPago.Value = oRstInfo.ObjSetRegistros.Fields("fecha").Value
            'DPFechaPago.MinDate = oRstInfo.ObjSetRegistros.Fields("fechaMin").Value
            'DPFechaPago.MaxDate = oRstInfo.ObjSetRegistros.Fields("fecha").Value
            oRstInfo.Cerrar
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstInfo.Cerrar
            Screen.MousePointer = vbDefault
            Exit Sub
    End Select

    bCerrarForm = False
    cmdExpExcel.Visible = False
    cmdImportacion.Visible = False
    dpFechaPago.Value = Date
    sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
    Call InicializarGrids
    Call BorrarFilasGrids
    cbEmpresa.ListIndex = 0
    cbCuentaBancaria.ListIndex = 0
    sstImportacion.Tab = 0
    lbMontoTab1.Caption = "$0.00"
    lbMontoTab2.Caption = "$0.00"
    lbMontoTab3.Caption = "$0.00"
    lbMontoTab4.Caption = "$0.00"
    lbMontoTab5.Caption = "$0.00"
    
    sDocImpActual = ""
    sDocImpAnterior = ""

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    pbarImportacion.Width = sbBarraEstado.Panels(2).Width - 40
    pbarImportacion.Top = sbBarraEstado.Top + 60
    pbarImportacion.Left = sbBarraEstado.Panels(1).Width + 80
    pbarImportacion.Height = sbBarraEstado.Height - 100
    pbContImportacion.Height = Height - sbBarraEstado.Height - 500
    
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
    Call BorrarFilasGrids
    cmdExpExcel.Visible = False
    
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
    
    Call BorrarFilasGrids
    cbCuentaBancaria.Clear
    cmdExpExcel.Visible = False
    
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
                    sConsultaSQL = sConsultaSQL & "and      a.tipo = 'D'" & vbNewLine
                    sConsultaSQL = sConsultaSQL & "and      a.cdgem = b.cdgem" & vbNewLine
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
        sConsultaSQL = sConsultaSQL & "and      a.tipo = 'D'" & vbNewLine
        sConsultaSQL = sConsultaSQL & "and      a.cdgem = b.cdgem" & vbNewLine
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
    Dim Periodo As Integer   'AMGM 25JUL2007
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    EjecutarImportacion = False
    
    lContador = 0
    lNoRegsExcel = fgImportacion.Rows - 1
    pbarImportacion.Value = 0
    pbarImportacion.Max = lNoRegsExcel
    lbMontoTab2.Caption = "$0.00"
    lbMontoTab3.Caption = "$0.00"
    lbMontoTab4.Caption = "$0.00"
    lbMontoTab5.Caption = "$0.00"
    dMontoTab2 = 0
    dMontoTab3 = 0
    dMontoTab4 = 0
    dMontoTab5 = 0
    
    With fgImportacion
        sbBarraEstado.Panels(1).Text = "Verificando los pagos a importar"
        For lCont = 1 To .Rows - 1
            .Row = lCont
            .Col = 1
            If (.CellPicture = pbPago.Picture) Then
                lContador = lContador + 1
            End If
        Next
        
        lNoPagosImp = lContador
    End With
    
    If (lContador = 0) Then
        Screen.MousePointer = vbDefault
        MsgBox "Debe existir por lo menos un pago válido para poder realizar la importación.", vbCritical + vbOKOnly, TITULO_MENSAJE
        sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
        EjecutarImportacion = True
        Exit Function
    End If
    
    lContador = 0
    lIndice = 0
    
    oAccesoDatos.cnn.IniciarTrans
    
    With fgImportacion
        sIdentificador = Format(Date, "DDMMYYYY") & Format(Time, "HHNNSS")
        iContImp = 0
        iSecuenciaImp = "0"
    
        For lCont = 1 To .Rows - 1
            lContador = lContador + 1
            DoEvents
            sbBarraEstado.Panels(1).Text = "Importando " & CStr(lContador) & " de " & CStr(lNoRegsExcel) & " registros  (" & Format((lContador / lNoRegsExcel) * 100, "##0.00") & "%)"
            pbarImportacion.Value = lContador
        
            .Row = lCont
            .Col = 1
            If (.CellPicture = pbPago.Picture) Then
                lIndice = lIndice + 1
                '-----   Verificamos si el pago ya existe en MP o PDI                     -----
                sCadenaSQL = ""
                sCadenaSQL = sCadenaSQL & "select /*+RULE*/ referencia " & vbNewLine
                sCadenaSQL = sCadenaSQL & "from   mp " & vbNewLine
                sCadenaSQL = sCadenaSQL & "where  cdgem       = '" & cbEmpresa.Text & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    frealdep    = '" & Format(.TextMatrix(lCont, 4), "YYYY/MM/DD") & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    cdgcb       = '" & .TextMatrix(lCont, 3) & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    referencia  = '" & .TextMatrix(lCont, 5) & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    cantidad    = '" & Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", "") & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "union  all" & vbNewLine
                sCadenaSQL = sCadenaSQL & "select /*+RULE*/ referencia " & vbNewLine
                sCadenaSQL = sCadenaSQL & "from   pdi " & vbNewLine
                sCadenaSQL = sCadenaSQL & "where  cdgem       = '" & cbEmpresa.Text & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    fdeposito   = '" & Format(.TextMatrix(lCont, 4), "YYYY/MM/DD") & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    cdgcb       = '" & .TextMatrix(lCont, 3) & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    referencia  = '" & .TextMatrix(lCont, 5) & "' " & vbNewLine
                sCadenaSQL = sCadenaSQL & "and    cantidad    = '" & Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", "") & "' "
                
                If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
                
                Select Case oRstPago.HayRegistros
                    Case 0  '-----   La consulta no retorno registros.    -----
                        '-----   El pago no existe en la B.D.   -----
                        bImportarPago = True
                        'bImportarPago = False
                    Case 1  '-----   Hay registros.                       -----
                        If (cPagoImp.Preguntar) Then
                            sFechaImp = Format(.TextMatrix(lCont, 4), "DD/MMM/YYYY")
                            sRefIM = .TextMatrix(lCont, 5)
                            dMontoPago = .TextMatrix(lCont, 6)
Preguntar:
                            frmMensaje.Show vbModal
                            
                            If (cPagoImp.Si Or cPagoImp.SiATodos) Then
                                'If (cPagoImp.SiATodos) Then cPagoImp.Preguntar = False
                                '-----   Importamos el pago                                   -----
                                bImportarPago = True
                            ElseIf (cPagoImp.No Or cPagoImp.NoATodos) Then
                                'If (cPagoImp.NoATodos) Then cPagoImp.Preguntar = False
                                '-----   No importamos el Pago                                -----
                                bImportarPago = False
                            ElseIf (cPagoImp.Cancelar) Then
                                '-----   Finalizamos el proceso y deshacemos la transacción   -----
                                Screen.MousePointer = vbDefault
                                respuesta = MsgBox("Esta por cancelarse el proceso de importación de pagos." & vbNewLine & "¿Está seguro(a) que desea hacerlo?", vbYesNo + vbQuestion, TITULO_MENSAJE)
                                Screen.MousePointer = vbHourglass
                                If (respuesta = vbYes) Then
                                    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                                    oAccesoDatos.cnn.DeshacerTrans
                                    'Call InicializarGrids
                                    fgArqueoCaja.Rows = 1
                                    fgArqueoCaja.Refresh
                                    fgIdentificados.Rows = 1
                                    fgIdentificados.Refresh
                                    fgNoIdentificados.Rows = 1
                                    fgNoIdentificados.Refresh
                                    fgNoImportados.Rows = 1
                                    fgNoImportados.Refresh
                                    lbDatoNoRegsTab2.Caption = "0"
                                    lbDatoNoRegsTab3.Caption = "0"
                                    lbDatoNoRegsTab4.Caption = "0"
                                    lbMontoTab2 = "$0.00"
                                    lbMontoTab3 = "$0.00"
                                    lbMontoTab4 = "$0.00"
                                    
                                    For lContador = 1 To .Rows - 1
                                        .Col = 1
                                        .Row = lContador
                                        
                                        If (.CellPicture = pbIdentificado.Picture Or .CellPicture = pbNoIdentificado.Picture Or .CellPicture = Me.pbNoImportado.Picture) Then
                                            Set .CellPicture = pbPago.Picture
                                            
                                            Select Case .TextMatrix(lContador, 8)
                                                'Case 0, 1, 2, 3, 4, 5, 6, 7
                                                Case "Identificado", "No Identificado", "No Importado", "Arqueo de caja", "No Validado", "Cancelar Importación"
                                                    .TextMatrix(lContador, 7) = ""
                                                    .Col = 8
                                                    .CellAlignment = flexAlignLeftCenter
                                                    .TextMatrix(lContador, 8) = "Por importar"
                                                    .TextMatrix(lContador, 9) = ""
                                            End Select
                                        End If
                                    Next
                                    
                                    sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
                                    pbarImportacion.Value = 0
                                    EjecutarImportacion = True
                                    Screen.MousePointer = vbDefault
                                    Exit Function
                                Else
                                    GoTo Preguntar
                                End If
                            End If
                        Else
                            If (cPagoImp.SiATodos) Then
                                '-----   Importamos el pago                                   -----
                                bImportarPago = True
                            ElseIf (cPagoImp.NoATodos) Then
                                '-----   No importamos el pago                                -----
                                bImportarPago = False
                            End If
                        End If
                    Case 2  '-----   El Query no se pudo ejecutar.        -----
                        Screen.MousePointer = vbDefault
                        MsgBox "No fue posible investigar la existencia del pago." & vbNewLine & vbNewLine & "debe consultar al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                        Screen.MousePointer = vbHourglass
                        bCerrarForm = True
                        Screen.MousePointer = vbDefault
                        Unload Me
                End Select
                If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                
                If (bImportarPago) Then
                        
                    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                                        
                    .TextMatrix(lCont, 5) = Trim(.TextMatrix(lCont, 5))
                 '>>AMGM 25JUL2007 Se implemento el spImportaPago
'                    '-----   Se generan los Recargos correspondientes - RETA04052006 -----
'                    If Len(Mid(.TextMatrix(lCont, 5), 2, 6)) = 6 And Len(Mid(.TextMatrix(lCont, 5), 8, 2)) = 2 Then
'                       sCadenaSQL = ""
'                       sCadenaSQL = "spValidaVencimiento ('" & cbEmpresa.Text & "','" & Mid(.TextMatrix(lCont, 5), 2, 6) & "','" & Mid(.TextMatrix(lCont, 5), 8, 2) & "'," & Mid(.TextMatrix(lCont, 5), 1, 1) & ",'" & sUsuarioApp & "')"
'                       'sUsuarioApp
'                       oAccesoDatos.cnn.Ejecutar sCadenaSQL
'                    End If
'                    '-----   Ejecutamos el proceso de importación del pago                -----
'                    sCadenaSQL = ""
'                    sCadenaSQL = "ProcesarPagos('" & Format(.TextMatrix(lCont, 4), "YYYY/MM/DD") & "', '" & .TextMatrix(lCont, 5) & "', " & Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", "") & ", '" & cbEmpresa.Text & "', '" & .TextMatrix(lCont, 3) & "', '" & sUsuarioApp & "', " & .TextMatrix(lCont, 0) & ", " & CStr(lIndice) & ", " & CStr(lNoPagosImp) & ", '" & sIdentificador & "')"
'                    oAccesoDatos.cnn.Ejecutar sCadenaSQL
'
'                    '-----   Ejecutamos proceso para registrar Movs Extraordinarios, si es que existe diferencia menor a los $0.50  -----
'                    If Len(Mid(.TextMatrix(lCont, 5), 2, 6)) = 6 And Len(Mid(.TextMatrix(lCont, 5), 8, 2)) = 2 Then
'                        sCadenaSQL = ""
'                        sCadenaSQL = Mid(.TextMatrix(lCont, 5), 1, 1)
'                        If sCadenaSQL = "0" Then
'                            sCadenaSQL = ""
'                            sCadenaSQL = "spRegDiferencia ('" & cbEmpresa.Text & "','" & Mid(.TextMatrix(lCont, 5), 2, 6) & "','" & Mid(.TextMatrix(lCont, 5), 8, 2) & "'," & Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", "") & ",'G','" & sUsuarioApp & "')"
'                            oAccesoDatos.cnn.Ejecutar sCadenaSQL
'                        ElseIf sCadenaSQL = "1" Then
'                            sCadenaSQL = ""
'                            sCadenaSQL = "spRegDiferencia ('" & cbEmpresa.Text & "','" & Mid(.TextMatrix(lCont, 5), 2, 6) & "','" & Mid(.TextMatrix(lCont, 5), 8, 2) & "'," & Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", "") & ",'I','" & sUsuarioApp & "')"
'                            oAccesoDatos.cnn.Ejecutar sCadenaSQL
'                        End If
'                    End If

'                    sCadenaSQL = "select NVL(max(periodo),0) + 1 as UltPeriodo"
'                    sCadenaSQL = sCadenaSQL & "  from mp "
'                    sCadenaSQL = sCadenaSQL & " where cdgem = '" & cbEmpresa.Text & "'"
'                    sCadenaSQL = sCadenaSQL & "   and cdgclns = '" & Mid(.TextMatrix(lCont, 5), 2, 6) & "'"
'                    sCadenaSQL = sCadenaSQL & "   and ciclo = '" & Mid(.TextMatrix(lCont, 5), 8, 2) & "'"
'                    sCadenaSQL = sCadenaSQL & "   and clns = '" & IIf(Mid(.TextMatrix(lCont, 5), 1, 1) = "0", "G", "I") & "'"
'
'                    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
'
'                    oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
'
'                    Select Case oRstPago.HayRegistros
'                        Case 0  '-----   La consulta no retorno registros.   -----
'
'                        Case 1  '-----   Hay registros.                       -----
'                            '-----   Llenamos el grid con la información encontrada   -----
'                            Periodo = oRstPago.ObjSetRegistros.Fields(0)
'                        Case 2  '-----   El Query no se pudo ejecutar.        -----
'                            Screen.MousePointer = vbDefault
'                            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
'                            Screen.MousePointer = vbHourglass
'                            oRstPago.Cerrar
'                            Screen.MousePointer = vbDefault
'                    End Select
                


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

                        .Parameters.Append .CreateParameter(, adVarChar, adParamOutput, 100)  'Resultado de la ejecución del SP
                        
                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Monto de la cancelacion
                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'RenExcel
                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Renglon
                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'NoPagos
                        .Parameters.Append .CreateParameter(, adNumeric, adParamInput, 30)  'Id Importacion  'AMGM 01NOV2009
                        
                        .Parameters.Append .CreateParameter(, adNumeric, adParamOutput, 2)
                        .Parameters.Append .CreateParameter(, adVarChar, adParamInput, 30)  'Moneda     'AMGM 2015 PLD
                        
                        .Parameters(0) = Format(fgImportacion.TextMatrix(lCont, 4), "YYYY/MM/DD") & Format(Time, " hh:mm:ss")
                        .Parameters(1) = fgImportacion.TextMatrix(lCont, 5)
                        .Parameters(2) = Replace(Replace(fgImportacion.TextMatrix(lCont, 6), "$", ""), ",", "")
                        .Parameters(3) = cbEmpresa.Text
                        .Parameters(4) = fgImportacion.TextMatrix(lCont, 3)
                        .Parameters(5) = sUsuarioApp
                        .Parameters(6) = sIdentificador
                        .Parameters(7) = 1
                        .Parameters(8) = "I"
                        .Parameters(10) = 0
                        .Parameters(11) = fgImportacion.TextMatrix(lCont, 0)
                        .Parameters(12) = lIndice
                        .Parameters(13) = lNoPagosImp
                        .Parameters(14) = iIdImportacion   ' AMGM 01NOV2009
                        .Parameters(16) = fgImportacion.TextMatrix(lCont, 10)   ' AMGM 2015 parametro Moneda por temas de PLD

                    End With
                    acmd.Execute
                    'MsgBox "Resultado = " & acmd.Parameters(9)

                    If acmd.Parameters(9) <> 1 Then
                            Set .CellPicture = pbNoImportado.Picture
                    End If
                    
                    If acmd.Parameters(15) <> 0 And acmd.Parameters(15) <> 1 Then
                  '<< AMGM 25JUL2007
                    '-----   Obtenemos el resultado del proceso de importación del pago   -----
                    sCadenaSQL = ""
                    sCadenaSQL = sCadenaSQL & "select /*+RULE*/ * " & vbNewLine
                    sCadenaSQL = sCadenaSQL & "from   res_impor " & vbNewLine
                    sCadenaSQL = sCadenaSQL & "where  cdgem         = '" & cbEmpresa.Text & "' " & vbNewLine
                    sCadenaSQL = sCadenaSQL & "and    fechapago     = '" & Format(.TextMatrix(lCont, 4), "DD/MM/YYYY") & "' " & vbNewLine
                    sCadenaSQL = sCadenaSQL & "and    referencia    = '" & .TextMatrix(lCont, 5) & "' " & vbNewLine
                    sCadenaSQL = sCadenaSQL & "and    ctabancaria   = '" & .TextMatrix(lCont, 3) & "' " & vbNewLine
                    sCadenaSQL = sCadenaSQL & "and    renexcel      = '" & .TextMatrix(lCont, 0) & "' " & vbNewLine
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
                            bCerrarForm = True
                            Screen.MousePointer = vbDefault
                            'Unload Me
                        Case 1  '-----   Hay registros.                       -----
                            .Col = 1
                            Select Case oRstPago.ObjSetRegistros.Fields("validacion").Value
                                Case 0, 6       'Identificado, Repetido (Identificado)
                                    Set .CellPicture = pbIdentificado.Picture
                                    lbMontoTab2.Caption = Format(CStr(CDbl(lbMontoTab2.Caption) + CDbl(Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", ""))), "$###,###,###,##0.00")
                                    lbDatoNoRegsTab2.Caption = CStr(CDbl(lbDatoNoRegsTab2.Caption) + 1)
                                Case 1, 7       'Garantias ' AMGM 09NOV2009
                                    Set .CellPicture = pbIdentificado.Picture
                                    lbMontoTab3.Caption = Format(CStr(CDbl(lbMontoTab3.Caption) + CDbl(Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", ""))), "$###,###,###,##0.00")
                                    lbDatoNoRegsTab3.Caption = CStr(CDbl(lbDatoNoRegsTab3.Caption) + 1)
                                Case 2          'No importado,No Identificado, Repetido (No Identificado) 'AMGM 09NOV2009
                                    Set .CellPicture = pbNoImportado.Picture
                                    lbMontoTab4.Caption = Format(CStr(CDbl(lbMontoTab4.Caption) + CDbl(Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", ""))), "$###,###,###,##0.00")
                                    lbDatoNoRegsTab4.Caption = CStr(CDbl(lbDatoNoRegsTab4.Caption) + 1)
                                Case 4          'Error en el proceso
                                    Set .CellPicture = pbNoImportado.Picture
                            End Select
                            
                            .Col = 7
                            .CellAlignment = flexAlignCenterCenter
                            .Text = Format(oRstPago.ObjSetRegistros.Fields("fechacarga").Value, "DD/MM/YYYY HH:NN:SS am/pm")
                            .Col = 8
                            .CellAlignment = flexAlignCenterCenter
                            
                            Dim intContIdentificado As Integer
                            Dim intContNoIdentificado As Integer
                            Dim intContNoImportado As Integer
                            
                            Select Case CStr(oRstPago.ObjSetRegistros.Fields("validacion").Value)
                                Case 0, 6       'Identificado, Duplicado Identificado
                                    .Text = "Identificado"
                                    fgIdentificados.Rows = fgIdentificados.Rows + 1
                                    fgIdentificados.Row = fgIdentificados.Rows - 1
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 0) = CStr(fgIdentificados.Row)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 1) = .TextMatrix(lCont, 0)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 2) = .TextMatrix(lCont, 7)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 3) = .TextMatrix(lCont, 3)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 4) = .TextMatrix(lCont, 4)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 5) = .TextMatrix(lCont, 5)
                                    fgIdentificados.Col = 6
                                    fgIdentificados.CellAlignment = flexAlignRightCenter
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 6) = .TextMatrix(lCont, 6)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 7) = IIf(IsNull(oRstPago.ObjSetRegistros.Fields("secueim").Value), "", oRstPago.ObjSetRegistros.Fields("secueim").Value)
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 8) = IIf(IsNull(oRstPago.ObjSetRegistros.Fields("secuemp").Value), "", oRstPago.ObjSetRegistros.Fields("secuemp").Value)
                                    fgIdentificados.Col = 9
                                    fgIdentificados.CellAlignment = flexAlignCenterCenter
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 9) = "Identificado"
                                    fgIdentificados.Col = 10
                                    fgIdentificados.CellAlignment = flexAlignLeftCenter
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 10) = Replace(Replace(Replace(Replace(oRstPago.ObjSetRegistros.Fields("msgresul").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", "")
                                    fgIdentificados.Col = 11 'AMGM 2015 Campo Moneda para PLD
                                    fgIdentificados.CellAlignment = flexAlignLeftCenter
                                    fgIdentificados.TextMatrix(fgIdentificados.Row, 11) = .TextMatrix(lCont, 10)
                                Case 1, 7       'No Identificado, Duplicado No Identificado
                                    .Text = "Garan. Liqu."
                                    fgNoIdentificados.Rows = fgNoIdentificados.Rows + 1
                                    fgNoIdentificados.Row = fgNoIdentificados.Rows - 1
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 0) = CStr(fgNoIdentificados.Row)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 1) = .TextMatrix(lCont, 0)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 2) = .TextMatrix(lCont, 7)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 3) = .TextMatrix(lCont, 3)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 4) = .TextMatrix(lCont, 4)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 5) = .TextMatrix(lCont, 5)
                                    fgNoIdentificados.Col = 6
                                    fgNoIdentificados.CellAlignment = flexAlignRightCenter
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 6) = .TextMatrix(lCont, 6)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 7) = IIf(IsNull(oRstPago.ObjSetRegistros.Fields("secueim").Value), "", oRstPago.ObjSetRegistros.Fields("secueim").Value)
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 8) = IIf(IsNull(oRstPago.ObjSetRegistros.Fields("secuepdi").Value), "", oRstPago.ObjSetRegistros.Fields("secuepdi").Value)
                                    fgNoIdentificados.Col = 9
                                    fgNoIdentificados.CellAlignment = flexAlignCenterCenter
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 9) = "Garantía"
                                    fgNoIdentificados.Col = 10
                                    fgNoIdentificados.CellAlignment = flexAlignLeftCenter
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 10) = Replace(Replace(Replace(Replace(oRstPago.ObjSetRegistros.Fields("msgresul").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", "")
                                    fgNoIdentificados.Col = 11 'AMGM 2015 Campo Moneda para PLD
                                    fgNoIdentificados.CellAlignment = flexAlignLeftCenter
                                    fgNoIdentificados.TextMatrix(fgNoIdentificados.Row, 11) = .TextMatrix(lCont, 10)
                                Case 2  'No Importado
                                    .Text = "No Importado"
                                    fgNoImportados.Rows = fgNoImportados.Rows + 1
                                    fgNoImportados.Row = fgNoImportados.Rows - 1
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 0) = CStr(fgNoImportados.Row)
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 1) = .TextMatrix(lCont, 0)
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 2) = .TextMatrix(lCont, 7)
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 3) = .TextMatrix(lCont, 3)
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 4) = .TextMatrix(lCont, 4)
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 5) = .TextMatrix(lCont, 5)
                                    fgNoImportados.Col = 6
                                    fgNoImportados.CellAlignment = flexAlignRightCenter
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 6) = .TextMatrix(lCont, 6)
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 7) = ""
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 8) = ""
                                    fgNoImportados.Col = 9
                                    fgNoImportados.CellAlignment = flexAlignCenterCenter
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 9) = "No Importado"
                                    fgNoImportados.Col = 10
                                    fgNoImportados.CellAlignment = flexAlignLeftCenter
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 10) = Replace(Replace(Replace(Replace(oRstPago.ObjSetRegistros.Fields("msgresul").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", "")
                                    fgNoImportados.Col = 11 'AMGM 2015 Campo Moneda para PLD
                                    fgNoImportados.CellAlignment = flexAlignLeftCenter
                                    fgNoImportados.TextMatrix(fgNoImportados.Row, 11) = .TextMatrix(lCont, 10)
                                Case 4  'No Validado
                                    .Text = "No Validado"
                                Case 5  'Cancelar Importación
                                    .Text = "Cancelar Importación"
                            End Select
                            
                            '.Text = CStr(oRstPago.ObjSetRegistros.Fields("validacion").Value)
                            .Col = 9
                            .CellAlignment = flexAlignLeftCenter
                            .Text = Replace(Replace(Replace(Replace(oRstPago.ObjSetRegistros.Fields("msgresul").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", "")
                            
                            '-----Se obtiene la Secuencia de Importacón para el resúmen - RETA23052006-----'
                            If iContImp = 0 Then
                                If IsNull(oRstPago.ObjSetRegistros.Fields("secueim").Value) Then
                                    iSecuenciaImp = "0"
                                Else
                                    iSecuenciaImp = Val(oRstPago.ObjSetRegistros.Fields("secueim").Value)
                                End If
                                iContImp = iContImp + 1
                            End If
                            
                        Case 2  '-----   El Query no se pudo ejecutar.        -----
                            Screen.MousePointer = vbDefault
                            MsgBox "No fue posible obtener el resultado de la validación" & vbNewLine & vbNewLine & "debe consultar al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                            Screen.MousePointer = vbHourglass
                            bCerrarForm = True
                            Screen.MousePointer = vbDefault
                            Unload Me
                    End Select
                    
                    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
                End If
            Else
                .Col = 1
                If (.CellPicture = pbNoValidado.Picture) Then
                    fgNoImportados.Rows = fgNoImportados.Rows + 1
                    fgNoImportados.Row = fgNoImportados.Rows - 1
                    fgNoImportados.Col = 10
                    fgNoImportados.CellAlignment = flexAlignLeftCenter
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 10) = .TextMatrix(lCont, 8)
                    .Col = 8
                    .CellAlignment = flexAlignCenterCenter
                    .Text = "No Importado"
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 0) = CStr(fgNoImportados.Row)
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 1) = .TextMatrix(lCont, 0)
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 2) = .TextMatrix(lCont, 7)
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 3) = .TextMatrix(lCont, 3)
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 4) = .TextMatrix(lCont, 4)
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 5) = .TextMatrix(lCont, 5)
                    fgNoImportados.Col = 6
                    fgNoImportados.CellAlignment = flexAlignRightCenter
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 6) = .TextMatrix(lCont, 6)
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 7) = ""
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 8) = ""
                    fgNoImportados.Col = 9
                    fgNoImportados.CellAlignment = flexAlignCenterCenter
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 9) = "No Importado"
                    fgNoImportados.Col = 11 'AMGM 2015 Campo Moneda para PLD
                    fgNoImportados.CellAlignment = flexAlignLeftCenter
                    fgNoImportados.TextMatrix(fgNoImportados.Row, 11) = .TextMatrix(lCont, 10)
                    
                    lbMontoTab4.Caption = Format(CStr(CDbl(lbMontoTab4.Caption) + CDbl(Replace(Replace(.TextMatrix(lCont, 6), "$", ""), ",", ""))), "$###,###,###,##0.00")
                    lbDatoNoRegsTab4.Caption = CStr(CDbl(lbDatoNoRegsTab4.Caption) + 1)
                End If
            End If
        Next
    End With
    
    MsgBox "Se importaron un total de " & CStr(lNoRegsExcel) & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
    sbBarraEstado.Panels(1).Text = TITULO_MOD_IMP
    pbarImportacion.Value = 0
    

    oAccesoDatos.cnn.AceptarTrans
    
 EjecutarImportacion = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Private Function ObtenerValidacion(ByVal poConexion As clsoAdoConexion, ByVal psFechaCarga As String, ByVal psFechaPago As String, ByVal psReferencia, ByVal psMonto As String, ByVal psEmpresa As String, ByVal psCtaBancaria As String, ByVal psUsuario As String, ByVal plRenExcel As Long) As Boolean
    Dim oRstVal As New clsoAdoRecordset
    Dim sCadenaSQL As String
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    ObtenerValidacion = False
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select * " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from   res_impor " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where  fechapago = '" & psFechaPago & "'" & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    referencia = '" & psReferencia & "'" & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    monto = " & psMonto & "" & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    cdgem = '" & psEmpresa & "'" & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    cdgcb = '" & psCtaBancaria & "'" & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    renexcel = '" & plRenExcel & "'" & vbNewLine
    
    oRstVal.Cerrar
    
    oRstVal.Abrir sCadenaSQL, poConexion.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstVal.HayRegistros
        Case 0  '-----   La consulta no retorno registros.    -----
            Screen.MousePointer = vbDefault
            MsgBox "No fue posible obtener el resultado de la validación" & vbNewLine & vbNewLine & "debe consultar al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            bCerrarForm = True
            Screen.MousePointer = vbDefault
            Unload Me
        Case 1  '-----   Hay registros.                       -----
            iStatus = CStr(oRstVal.ObjSetRegistros.Fields("validacion").Value)
            sMensaje = oRstVal.ObjSetRegistros.Fields("msgresul").Value
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "No fue posible obtener el resultado de la validación" & vbNewLine & vbNewLine & "debe consultar al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            bCerrarForm = True
            Screen.MousePointer = vbDefault
            Unload Me
    End Select
    
    oRstVal.Cerrar
    
    ObtenerValidacion = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    poConexion.DeshacerTrans
    MensajeError Err
End Function

Private Function EliminarPago(ByVal poConexion As clsoAdoConexion, ByVal psFechaCarga As String, ByVal psFechaPago As String, ByVal psReferencia, ByVal psMonto As String, ByVal psEmpresa As String, ByVal psCtaBancaria As String, ByVal psUsuario As String, ByVal psArchivo As String) As Boolean
    Dim sCadenaSQL As String
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    EliminarPago = False
    
    Select Case iStatus
        Case 6
            sCadenaSQL = "delete from mp where"
        Case 7
            sCadenaSQL = "delete from pdi where "
    End Select
    
    poConexion.Ejecutar sCadenaSQL
    
    EliminarPago = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    poConexion.DeshacerTrans
    MensajeError Err
End Function

Private Function ImportarPago(ByVal poConexion As clsoAdoConexion, ByVal psFechaCarga As String, ByVal psFechaPago As String, ByVal psReferencia, ByVal psMonto As String, ByVal psEmpresa As String, ByVal psCtaBancaria As String, ByVal psUsuario As String, ByVal psSecIM As String, ByVal plRenExcel As Long) As Boolean
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    ImportarPago = False

    sCadenaSQL = "ProcesarPagos('" & psFechaCarga & "', '" & psFechaPago & "', '" & psReferencia & "', " & psMonto & ", '" & psEmpresa & "', '" & psCtaBancaria & "', '" & psUsuario & "', '" & psSecIM & "', " & plRenExcel & ")"
    
    poConexion.Ejecutar sCadenaSQL

    ImportarPago = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    poConexion.DeshacerTrans
    MensajeError Err
End Function

Private Sub ActualizarRegIM(ByVal poConexion As clsoAdoConexion)
    Dim sCadenaSQL As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    '-----   Construimos la consulta para insertar el registro en la Tabla IM   -----
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "update IM " & vbNewLine
    sCadenaSQL = sCadenaSQL & "set    nummov      = " & CStr(CDbl(lbDatoNoRegsTab2.Caption) + CDbl(lbDatoNoRegsTab3.Caption)) & ", " & vbNewLine
    sCadenaSQL = sCadenaSQL & "       importe     = " & CStr(CDbl(lbMontoTab2.Caption) + CDbl(lbMontoTab3.Caption)) & ", " & vbNewLine
    sCadenaSQL = sCadenaSQL & "       nummovorig  = " & CStr(CDbl(lbDatoNoRegsTab2.Caption) + CDbl(lbDatoNoRegsTab3.Caption)) & ", " & vbNewLine
    sCadenaSQL = sCadenaSQL & "       importeorig = " & CStr(CDbl(lbMontoTab2.Caption) + CDbl(lbMontoTab3.Caption)) & " " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where  cdgem       = '" & cbEmpresa.Text & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    cdgcb       = '" & Mid(cbCuentaBancaria.Text, 1, 2) & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    fecha       = '" & Format(dpFechaPago.Value, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    secuencia   = '" & sSecuenciaIM & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    importape   = '" & sUsuarioApp & "'" & vbNewLine
    
    poConexion.Ejecutar sCadenaSQL

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub ActualizarRegMB(ByVal poConexion As clsoAdoConexion)
    Dim sCadenaSQL As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    '-----   Construimos la consulta para insertar el registro en la Tabla IM   -----
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "update mb " & vbNewLine
    sCadenaSQL = sCadenaSQL & "set    cargo                = " & CStr(CDbl(lbMontoTab2.Caption) + CDbl(lbMontoTab3.Caption)) & ", " & vbNewLine
    sCadenaSQL = sCadenaSQL & "       actualizarpe         = '" & sUsuarioApp & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where  cdgem                = '" & cbEmpresa.Text & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    cdgcb                = '" & Mid(cbCuentaBancaria.Text, 1, 2) & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    fecha                = '" & Format(dpFechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    to_number(secuencia) = " & CDbl(sSecuenciaIM) & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    referencia           = '*'" & vbNewLine
    sCadenaSQL = sCadenaSQL & "and    tipo                 = 'D'" & vbNewLine
    
    poConexion.Ejecutar sCadenaSQL

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Function InicializarGrids() As Boolean
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    InicializarGrids = False
    
    'Inicialización del Grid para pagos procesados
    'AMGM 2015 Se agrego la columna del tipo de moneda para PLD
    With fgImportacion
        .Rows = 1
        .Cols = 11
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 500
        
        .Col = 1
        .TextMatrix(0, 1) = "Status"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 600
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Carga"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 2300
        
        .Col = 3
        .TextMatrix(0, 3) = "Cta. Bancaria"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Fecha de Pago"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1300
        
        .Col = 5
        .TextMatrix(0, 5) = "Referencia"
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1200
        
        .Col = 6
        .TextMatrix(0, 6) = "Monto"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1300
        
        .Col = 7
        .TextMatrix(0, 7) = "Fecha Importación"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 2300
        
        .Col = 8
        .TextMatrix(0, 8) = "Validación"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 1500
        
        .Col = 9
        .TextMatrix(0, 9) = "Mensaje de validación"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 5000
        
        .Col = 10
        .TextMatrix(0, 10) = "Moneda"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(9) = 600
    End With
    
    'Inicialización del Grid para pagos identificados
    'AMGM 2015 Se agrego la columna del tipo de moneda para PLD
    With fgIdentificados
        .Rows = 1
        .Cols = 12
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 500
        
        .Col = 1
        .TextMatrix(0, 1) = "Ren. Excel"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 1400
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Carga"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 2300
        
        .Col = 3
        .TextMatrix(0, 3) = "Cta. Bancaria"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Fecha de Pago"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1300
        
        .Col = 5
        .TextMatrix(0, 5) = "Referencia"
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1200
        
        .Col = 6
        .TextMatrix(0, 6) = "Monto"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1300
        
        .Col = 7
        .TextMatrix(0, 7) = "Sec. IM"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 800
        
        .Col = 8
        .TextMatrix(0, 8) = "Sec. MP"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 800
        
        .Col = 9
        .TextMatrix(0, 9) = "Validación"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 1500
        
        .Col = 10
        .TextMatrix(0, 10) = "Mensaje de validación"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 5000
        
        .Col = 11
        .TextMatrix(0, 11) = "Moneda"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(9) = 600
    End With
    
    'Inicialización del Grid para pagos NO identificados
    'AMGM 2015 Se agrego la columna del tipo de moneda para PLD
    With fgNoIdentificados
        .Rows = 1
        .Cols = 12
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 500
        
        .Col = 1
        .TextMatrix(0, 1) = "Ren. Excel"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 1400
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Carga"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 2300
        
        .Col = 3
        .TextMatrix(0, 3) = "Cta. Bancaria"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Fecha de Pago"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1300
        
        .Col = 5
        .TextMatrix(0, 5) = "Referencia"
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1200
        
        .Col = 6
        .TextMatrix(0, 6) = "Monto"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1300
        
        .Col = 7
        .TextMatrix(0, 7) = "Sec. IM"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 800
        
        .Col = 8
        .TextMatrix(0, 8) = "Sec. MP"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 800
        
        .Col = 9
        .TextMatrix(0, 9) = "Validación"
        .ColAlignment(9) = flexAlignLeftCenter
        .ColWidth(9) = 1500
        
        .Col = 10
        .TextMatrix(0, 10) = "Mensaje de validación"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 5000
        
        .Col = 11
        .TextMatrix(0, 11) = "Moneda"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(9) = 600
    End With
    
    'Inicialización del Grid para pagos NO importados
    'AMGM 2015 Se agrego la columna del tipo de moneda para PLD
    With fgNoImportados
        .Rows = 1
        .Cols = 12
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 500
        
        .Col = 1
        .TextMatrix(0, 1) = "Ren. Excel"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 1400
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Carga"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 2300
        
        .Col = 3
        .TextMatrix(0, 3) = "Cta. Bancaria"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Fecha de Pago"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1300
        
        .Col = 5
        .TextMatrix(0, 5) = "Referencia"
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1200
        
        .Col = 6
        .TextMatrix(0, 6) = "Monto"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1300
        
        .Col = 7
        .TextMatrix(0, 7) = "Sec. IM"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 800
        
        .Col = 8
        .TextMatrix(0, 8) = "Sec. MP"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 800
        
        .Col = 9
        .TextMatrix(0, 9) = "Validación"
        .ColAlignment(9) = flexAlignLeftCenter
        .ColWidth(9) = 1500
        
        .Col = 10
        .TextMatrix(0, 10) = "Mensaje de validación"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 5000
        
        .Col = 11
        .TextMatrix(0, 11) = "Moneda"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(9) = 600
    End With
    
    'Inicialización del Grid para pagos de Arqueo de Caja
    With fgArqueoCaja
        .Rows = 1
        .Cols = 11
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 500
        
        .Col = 1
        .TextMatrix(0, 1) = "Ren. Excel"
        .ColAlignment(1) = flexAlignCenterCenter
        .ColWidth(1) = 1400
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Carga"
        .ColAlignment(2) = flexAlignCenterCenter
        .ColWidth(2) = 1300
        
        .Col = 3
        .TextMatrix(0, 3) = "Cta. Bancaria"
        .ColAlignment(3) = flexAlignCenterCenter
        .ColWidth(3) = 1200
        
        .Col = 4
        .TextMatrix(0, 4) = "Fecha de Pago"
        .ColAlignment(4) = flexAlignCenterCenter
        .ColWidth(4) = 1300
        
        .Col = 5
        .TextMatrix(0, 5) = "Referencia"
        .ColAlignment(5) = flexAlignCenterCenter
        .ColWidth(5) = 1200
        
        .Col = 6
        .TextMatrix(0, 6) = "Monto"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 1300
        
        .Col = 7
        .TextMatrix(0, 7) = "Sec. IM"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 800
        
        .Col = 8
        .TextMatrix(0, 8) = "Sec. MP"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 800
        
        .Col = 9
        .TextMatrix(0, 9) = "Validación"
        .ColAlignment(9) = flexAlignLeftCenter
        .ColWidth(9) = 1500
        
        .Col = 10
        .TextMatrix(0, 10) = "Mensaje de validación"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 5000
    End With
    
    InicializarGrids = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Private Function BorrarFilasGrids() As Boolean
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    BorrarFilasGrids = False
    
    'Inicialización del Grid fgImportacion
    With fgImportacion
        .Rows = 1
        .Refresh
    End With
    lbDatoNoRegsTab1.Caption = "0"
    
    'Inicialización del Grid fgIdentificados
    With fgIdentificados
        .Rows = 1
        .Refresh
    End With
    lbDatoNoRegsTab2.Caption = "0"
    
    'Inicialización del Grid fgNoIdentificados
    With fgNoIdentificados
        .Rows = 1
        .Refresh
    End With
    lbDatoNoRegsTab3.Caption = "0"
    
    'Inicialización del Grid fgNoImportados
    With fgNoImportados
        .Rows = 1
        .Refresh
    End With
    lbDatoNoRegsTab4.Caption = "0"
    
    'Inicialización del Grid fgArqueoCaja
    With fgArqueoCaja
        .Rows = 1
        .Refresh
    End With
    lbDatoNoRegsTab5.Caption = "0"
    
    BorrarFilasGrids = True
    Screen.MousePointer = vbDefault
    Exit Function
RutinaError:
    MensajeError Err
End Function

Private Function ImportacionTotales() As Boolean
    Dim oRstPago As New clsoAdoRecordset

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    ImportacionTotales = False

    bImportacion = False

    sImportarSQL = ""
    sImportarSQL = "SELECT (MAX(ID_IMPORTACION) + 1) MAXIMO "
    sImportarSQL = sImportarSQL & "FROM ImportacionPag "
    sImportarSQL = sImportarSQL & "WHERE CDGEM = '" & cbEmpresa.Text & "' AND "
    sImportarSQL = sImportarSQL & "CTA_BANCARIA = " & Mid(cbCuentaBancaria.Text, 1, 2)

    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
    oRstPago.Abrir sImportarSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    Select Case oRstPago.HayRegistros
        Case 0
            iIdImportacion = 1
            bImportacion = True
        Case 1
            If IsNull(oRstPago.ObjSetRegistros.Fields(0)) Then
                iIdImportacion = 1
            Else
                iIdImportacion = oRstPago.ObjSetRegistros.Fields(0)
            End If
            bImportacion = True
        Case 2
            bImportacion = False
    End Select
    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar

    sImportarSQL = ""
    sImportarSQL = "spInsImportacion (0,'" & cbEmpresa.Text & "'," & iIdImportacion & ","
    sImportarSQL = sImportarSQL & Mid(cbCuentaBancaria.Text, 1, 2) & ","
    sImportarSQL = sImportarSQL & lbDatoNoRegsTab1.Caption & ",0,'"
    sImportarSQL = sImportarSQL & Format(sFechaCarga, "YYYY/MM/DD") & "','"
    sImportarSQL = sImportarSQL & Format(dpFechaPago.Value, "YYYY/MM/DD") & "',"
    sImportarSQL = sImportarSQL & Format(lbMontoTab1.Caption, "#########.00") & ")"
    oAccesoDatos.cnn.Ejecutar sImportarSQL

    ImportacionTotales = True
    Screen.MousePointer = vbDefault
    Exit Function
    
RutinaError:
    bImportacion = False
    Screen.MousePointer = vbDefault
    MensajeError Err
End Function
    
Private Function ImportacionTotalesDet() As Boolean
    Dim oRstPago As New clsoAdoRecordset

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    ImportacionTotalesDet = False

    If bImportacion = True Then
        
        'PAGOS IDENTIFICADOS
        If Val(lbDatoNoRegsTab2.Caption) > 0 Then
            sImportarDetSQL = ""
            sImportarDetSQL = "spInsImportacion (1,'" & cbEmpresa.Text & "'," & iIdImportacion & ","
            sImportarDetSQL = sImportarDetSQL & Mid(cbCuentaBancaria.Text, 1, 2) & ","
            sImportarDetSQL = sImportarDetSQL & lbDatoNoRegsTab2.Caption & ",1,"
            sImportarDetSQL = sImportarDetSQL & "NULL,"
            sImportarDetSQL = sImportarDetSQL & "NULL,"
            sImportarDetSQL = sImportarDetSQL & Format(lbMontoTab2.Caption, "#########.00") & ")"
            oAccesoDatos.cnn.Ejecutar sImportarDetSQL
        End If
        
        'PAGOS NO IDENTIFICADOS
        If Val(lbDatoNoRegsTab3.Caption) > 0 Then
            sImportarDetSQL = ""
            sImportarDetSQL = "spInsImportacion (1,'" & cbEmpresa.Text & "'," & iIdImportacion & ","
            sImportarDetSQL = sImportarDetSQL & Mid(cbCuentaBancaria.Text, 1, 2) & ","
            sImportarDetSQL = sImportarDetSQL & lbDatoNoRegsTab3.Caption & ",2,"
            sImportarDetSQL = sImportarDetSQL & "NULL,"
            sImportarDetSQL = sImportarDetSQL & "NULL,"
            sImportarDetSQL = sImportarDetSQL & Format(lbMontoTab3.Caption, "#########.00") & ")"
            oAccesoDatos.cnn.Ejecutar sImportarDetSQL
        End If
        
        'PAGOS NO IMPORTADOS
        If Val(lbDatoNoRegsTab4.Caption) > 0 Then
            sImportarDetSQL = ""
            sImportarDetSQL = "spInsImportacion (1,'" & cbEmpresa.Text & "'," & iIdImportacion & ","
            sImportarDetSQL = sImportarDetSQL & Mid(cbCuentaBancaria.Text, 1, 2) & ","
            sImportarDetSQL = sImportarDetSQL & lbDatoNoRegsTab4.Caption & ",3,"
            sImportarDetSQL = sImportarDetSQL & "NULL,"
            sImportarDetSQL = sImportarDetSQL & "NULL,"
            sImportarDetSQL = sImportarDetSQL & Format(lbMontoTab4.Caption, "#########.00") & ")"
            oAccesoDatos.cnn.Ejecutar sImportarDetSQL
        End If
        
        'sImportarDetSQL = ""
        'sImportarDetSQL = "spUpdImportacion ('" & cbEmpresa.Text & "'," & iIdImportacion & ","
        'sImportarDetSQL = sImportarDetSQL & Mid(cbCuentaBancaria.Text, 1, 2) & ",'" & iSecuenciaImp & "')"
        'oAccesoDatos.cnn.Ejecutar sImportarDetSQL
        
    End If
    
    ImportacionTotalesDet = True
    Screen.MousePointer = vbDefault
    Exit Function
    
RutinaError:
    Screen.MousePointer = vbDefault
    MensajeError Err
End Function
    
