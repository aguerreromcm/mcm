VERSION 5.00
Begin VB.UserControl ctlMensajeApp 
   ClientHeight    =   1845
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   5505
   ScaleHeight     =   1845
   ScaleWidth      =   5505
   ToolboxBitmap   =   "ctlMensajeApp.ctx":0000
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00A00000&
      BorderStyle     =   0  'None
      Height          =   525
      Left            =   0
      ScaleHeight     =   537.5
      ScaleMode       =   0  'User
      ScaleWidth      =   5505
      TabIndex        =   0
      Top             =   0
      Width           =   5505
      Begin VB.PictureBox Picture1 
         Height          =   495
         Left            =   120
         Picture         =   "ctlMensajeApp.ctx":0312
         ScaleHeight     =   435
         ScaleWidth      =   1035
         TabIndex        =   16
         Top             =   0
         Width           =   1095
      End
      Begin VB.Label Label1 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Mensaje del sistema"
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
         Left            =   1260
         TabIndex        =   1
         Top             =   150
         Width           =   1995
      End
   End
   Begin VB.PictureBox pbMensaje 
      Align           =   2  'Align Bottom
      Appearance      =   0  'Flat
      BackColor       =   &H00FFFFFF&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   1320
      Left            =   0
      ScaleHeight     =   1320
      ScaleWidth      =   5505
      TabIndex        =   2
      Top             =   525
      Width           =   5505
      Begin VB.CommandButton cmdBoton3 
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
         Left            =   3150
         TabIndex        =   6
         Top             =   900
         Width           =   1000
      End
      Begin VB.CommandButton cmdBoton2 
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
         Left            =   2100
         TabIndex        =   5
         Top             =   900
         Width           =   1000
      End
      Begin VB.CommandButton cmdBoton1 
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
         Left            =   1050
         TabIndex        =   4
         Top             =   900
         Width           =   1000
      End
      Begin VB.PictureBox Picture4 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         BorderStyle     =   0  'None
         ForeColor       =   &H80000008&
         Height          =   480
         Left            =   120
         Picture         =   "ctlMensajeApp.ctx":08E7
         ScaleHeight     =   480
         ScaleWidth      =   480
         TabIndex        =   3
         Top             =   180
         Width           =   480
      End
      Begin VB.Label lbMensaje 
         BackStyle       =   0  'Transparent
         Caption         =   "Contenido"
         Height          =   255
         Left            =   720
         TabIndex        =   15
         Top             =   180
         Width           =   4335
         WordWrap        =   -1  'True
      End
   End
   Begin VB.PictureBox pbInformacion 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   4110
      Picture         =   "ctlMensajeApp.ctx":1529
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   14
      Top             =   2010
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
      Left            =   3540
      Picture         =   "ctlMensajeApp.ctx":216B
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   13
      Top             =   2010
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
      Left            =   2970
      Picture         =   "ctlMensajeApp.ctx":2DAD
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   12
      Top             =   2010
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
      Left            =   2400
      Picture         =   "ctlMensajeApp.ctx":39EF
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   11
      Top             =   2010
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
      Left            =   4110
      Picture         =   "ctlMensajeApp.ctx":4631
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   10
      Top             =   1440
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
      Left            =   3540
      Picture         =   "ctlMensajeApp.ctx":A383
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   9
      Top             =   1440
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
      Left            =   2970
      Picture         =   "ctlMensajeApp.ctx":100D5
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   8
      Top             =   1440
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.PictureBox pbErrorIco 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   2400
      Picture         =   "ctlMensajeApp.ctx":15CEF
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   7
      Top             =   1440
      Visible         =   0   'False
      Width           =   480
   End
End
Attribute VB_Name = "ctlMensajeApp"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private mcMensaje As New clsoMensajeApp

Public Property Get Alto() As Long
    Alto = mcMensaje.Alto
End Property

Public Property Get Ancho() As Long
    Ancho = mcMensaje.Ancho
End Property

Private Sub UserControl_Initialize()
    mcMensaje.Alto = 1860
    mcMensaje.Ancho = 5200
End Sub

Private Sub UserControl_Resize()
    UserControl.Width = mcMensaje.Ancho
    UserControl.Height = mcMensaje.Alto
End Sub
