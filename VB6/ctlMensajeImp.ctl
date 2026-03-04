VERSION 5.00
Begin VB.UserControl ctlMensajeImp 
   BackColor       =   &H00FFFFFF&
   ClientHeight    =   3330
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   6405
   ScaleHeight     =   3330
   ScaleWidth      =   6405
   ToolboxBitmap   =   "ctlMensajeImp.ctx":0000
   Begin VB.PictureBox pbContenido 
      Align           =   2  'Align Bottom
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   2800
      Left            =   0
      ScaleHeight     =   2805
      ScaleWidth      =   6405
      TabIndex        =   10
      Top             =   525
      Width           =   6405
      Begin VB.PictureBox Picture4 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         BorderStyle     =   0  'None
         ForeColor       =   &H80000008&
         Height          =   480
         Left            =   120
         Picture         =   "ctlMensajeImp.ctx":0312
         ScaleHeight     =   480
         ScaleWidth      =   480
         TabIndex        =   16
         Top             =   120
         Width           =   480
      End
      Begin VB.CommandButton cmdSi 
         Caption         =   "&Si"
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
         Left            =   60
         TabIndex        =   15
         Top             =   2280
         Width           =   1000
      End
      Begin VB.CommandButton cmdNo 
         Caption         =   "&No"
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
         Left            =   1110
         TabIndex        =   14
         Top             =   2280
         Width           =   1000
      End
      Begin VB.CommandButton cmdSiATodos 
         Caption         =   "S&i a todos"
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
         Left            =   2160
         TabIndex        =   13
         Top             =   2280
         Width           =   1000
      End
      Begin VB.CommandButton cmdNoATodos 
         Caption         =   "N&o a todos"
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
         Left            =   3210
         TabIndex        =   12
         Top             =   2280
         Width           =   1000
      End
      Begin VB.CommandButton cmdCancelar 
         Caption         =   "&Cancelar la importación"
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
         Left            =   4260
         TabIndex        =   11
         Top             =   2280
         Width           =   2000
      End
      Begin VB.Label lbDato 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Ya existe en la Base de Datos un pago con la siguiente información:"
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
         Index           =   0
         Left            =   810
         TabIndex        =   24
         Top             =   270
         Width           =   5085
      End
      Begin VB.Label lbDato 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Fecha:"
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
         Index           =   1
         Left            =   2550
         TabIndex        =   23
         Top             =   720
         Width           =   510
      End
      Begin VB.Label lbDato 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Referencia:"
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
         Index           =   3
         Left            =   2220
         TabIndex        =   22
         Top             =   990
         Width           =   855
      End
      Begin VB.Label lbDato 
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
         Index           =   5
         Left            =   2550
         TabIndex        =   21
         Top             =   1260
         Width           =   525
      End
      Begin VB.Label lbDato 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "10/08/2005"
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
         Index           =   2
         Left            =   3120
         TabIndex        =   20
         Top             =   720
         Width           =   1100
      End
      Begin VB.Label lbDato 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "000000000"
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
         Index           =   4
         Left            =   3120
         TabIndex        =   19
         Top             =   990
         Width           =   1100
      End
      Begin VB.Label lbDato 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "$0.00"
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
         Index           =   6
         Left            =   3120
         TabIndex        =   18
         Top             =   1260
         Width           =   1100
      End
      Begin VB.Label lbDato 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "¿Desea importar el pago?"
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
         Index           =   7
         Left            =   2190
         TabIndex        =   17
         Top             =   1740
         Width           =   1920
      End
   End
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00A00000&
      BorderStyle     =   0  'None
      Height          =   525
      Left            =   0
      ScaleHeight     =   537.5
      ScaleMode       =   0  'User
      ScaleWidth      =   6405
      TabIndex        =   8
      Top             =   0
      Width           =   6405
      Begin VB.PictureBox Picture1 
         Height          =   495
         Left            =   360
         Picture         =   "ctlMensajeImp.ctx":0F54
         ScaleHeight     =   435
         ScaleWidth      =   1035
         TabIndex        =   25
         Top             =   0
         Width           =   1095
      End
      Begin VB.Label Label1 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Importación de Pagos"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00FFFFFF&
         Height          =   195
         Left            =   1800
         TabIndex        =   9
         Top             =   150
         Width           =   2145
      End
   End
   Begin VB.PictureBox pbErrorIco 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   2430
      Picture         =   "ctlMensajeImp.ctx":1529
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   7
      Top             =   5610
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbPreguntaIco 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   3000
      Picture         =   "ctlMensajeImp.ctx":727B
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   6
      Top             =   5610
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbPrecaucionIco 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   3570
      Picture         =   "ctlMensajeImp.ctx":CE95
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   5
      Top             =   5610
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbInformacionIco 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   4140
      Picture         =   "ctlMensajeImp.ctx":12BE7
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   4
      Top             =   5610
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbError 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   2430
      Picture         =   "ctlMensajeImp.ctx":18939
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   3
      Top             =   6180
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbPregunta 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   3000
      Picture         =   "ctlMensajeImp.ctx":1957B
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   2
      Top             =   6180
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbPrecaucion 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   3570
      Picture         =   "ctlMensajeImp.ctx":1A1BD
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   1
      Top             =   6180
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbInformacion 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   4140
      Picture         =   "ctlMensajeImp.ctx":1ADFF
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   0
      Top             =   6180
      Visible         =   0   'False
      Width           =   480
   End
End
Attribute VB_Name = "ctlMensajeImp"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Public Event ClickCancelar()
Public Event ClickSi()
Public Event ClickNo()
Public Event ClickSiATodos()
Public Event ClickNoATodos()

Private msFecha As String
Private msReferencia As String
Private msMonto As String

Public Property Get Fecha() As String
    Fecha = msFecha
End Property

Public Property Let Fecha(psValor As String)
    msFecha = psValor
    lbDato(2).Caption = psValor
End Property

Public Property Get Referencia() As String
    Referencia = msFecha
End Property

Public Property Let Referencia(psValor As String)
    msReferencia = psValor
    lbDato(4).Caption = psValor
End Property

Public Property Get Monto() As String
    Monto = msFecha
End Property

Public Property Let Monto(psValor As String)
    msMonto = psValor
    lbDato(6).Caption = psValor
End Property

Private Sub cmdCancelar_Click()
    RaiseEvent ClickCancelar
End Sub

Private Sub cmdNo_Click()
    RaiseEvent ClickNo
End Sub

Private Sub cmdNoATodos_Click()
    RaiseEvent ClickNoATodos
End Sub

Private Sub cmdSi_Click()
    RaiseEvent ClickSi
End Sub

Private Sub cmdSiATodos_Click()
    RaiseEvent ClickSiATodos
End Sub

Private Sub UserControl_Initialize()
    msFecha = ""
    msReferencia = ""
    msMonto = "0"
End Sub

Private Sub UserControl_Resize()
    UserControl.Width = 6400
    UserControl.Height = 3330
    'pbEncabezado.Height = 860
    'pbContenido.Height = UserControl.Height - (pbEncabezado.Height + 20)
End Sub

