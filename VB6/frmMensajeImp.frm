VERSION 5.00
Begin VB.Form frmMensajeImp 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de importación y conciliación de pagos"
   ClientHeight    =   3450
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   5250
   Icon            =   "frmMensajeImp.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3450
   ScaleWidth      =   5250
   StartUpPosition =   3  'Windows Default
   Begin VB.PictureBox pbErrorIco 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   480
      Left            =   780
      Picture         =   "frmMensajeImp.frx":5D52
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   7
      Top             =   2010
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
      Left            =   1350
      Picture         =   "frmMensajeImp.frx":BAA4
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   6
      Top             =   2010
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
      Left            =   1920
      Picture         =   "frmMensajeImp.frx":116BE
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   5
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
      Left            =   2490
      Picture         =   "frmMensajeImp.frx":17410
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   4
      Top             =   2010
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
      Left            =   2490
      Picture         =   "frmMensajeImp.frx":1D162
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   3
      Top             =   2580
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
      Left            =   1920
      Picture         =   "frmMensajeImp.frx":1DDA4
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   2
      Top             =   2580
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
      Left            =   1350
      Picture         =   "frmMensajeImp.frx":1E9E6
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   1
      Top             =   2580
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
      Left            =   780
      Picture         =   "frmMensajeImp.frx":1F628
      ScaleHeight     =   480
      ScaleWidth      =   480
      TabIndex        =   0
      Top             =   2580
      Visible         =   0   'False
      Width           =   480
   End
End
Attribute VB_Name = "frmMensajeImp"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
