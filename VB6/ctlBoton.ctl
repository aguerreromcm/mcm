VERSION 5.00
Begin VB.UserControl ctlBoton 
   BackColor       =   &H00FFFFFF&
   BackStyle       =   0  'Transparent
   ClientHeight    =   780
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   5220
   DefaultCancel   =   -1  'True
   DrawStyle       =   5  'Transparent
   ScaleHeight     =   780
   ScaleWidth      =   5220
   Begin VB.Label lbBoton 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "Etiqueta"
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
      Height          =   195
      Left            =   0
      TabIndex        =   0
      Top             =   30
      Width           =   1215
   End
   Begin VB.Image imgBoton 
      Height          =   270
      Left            =   0
      Picture         =   "ctlBoton.ctx":0000
      Top             =   0
      Width           =   1260
   End
   Begin VB.Image imgBotonPresionado 
      Height          =   270
      Left            =   3930
      Picture         =   "ctlBoton.ctx":06C0
      Top             =   0
      Width           =   1260
   End
   Begin VB.Image imgBotonIluminado 
      Height          =   270
      Left            =   2670
      Picture         =   "ctlBoton.ctx":0AE8
      Top             =   0
      Width           =   1260
   End
   Begin VB.Image imgBotonNormal 
      Height          =   270
      Left            =   1410
      Picture         =   "ctlBoton.ctx":11A9
      Top             =   0
      Width           =   1260
   End
End
Attribute VB_Name = "ctlBoton"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Event BotonClick()
Private mTexto As String
Private mHabilitado As Boolean

Property Get Texto() As String
    Texto = mTexto
End Property

Property Let Texto(psValor As String)
    mTexto = psValor
    lbBoton.Caption = mTexto
End Property

Property Get Habilitado() As Boolean
    Habilitado = mHabilitado
End Property

Property Let Habilitado(pbValor As Boolean)
    mHabilitado = pbValor
    lbBoton.Enabled = mHabilitado
End Property

Private Sub lbBoton_Click()
    RaiseEvent BotonClick
End Sub

Private Sub lbBoton_MouseDown(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Set imgBoton.Picture = imgBotonPresionado.Picture
End Sub

Private Sub lbBoton_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Set imgBoton.Picture = imgBotonNormal.Picture
End Sub

Private Sub UserControl_Resize()
    imgBoton.Left = 0
    imgBoton.Top = 0
    lbBoton.Left = 0
    lbBoton.Top = 30
    Width = imgBoton.Width
    Height = imgBoton.Height
    lbBoton.Height = imgBoton.Height
    lbBoton.Width = imgBoton.Width
End Sub
