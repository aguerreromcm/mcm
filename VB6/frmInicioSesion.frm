VERSION 5.00
Begin VB.Form frmInicioSesion 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Inicio de Sesión"
   ClientHeight    =   2040
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   3540
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmInicioSesion.frx":0000
   LinkTopic       =   "Form1"
   LockControls    =   -1  'True
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   2040
   ScaleWidth      =   3540
   StartUpPosition =   2  'CenterScreen
   Begin VB.ComboBox cbAmbiente 
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
      ItemData        =   "frmInicioSesion.frx":0442
      Left            =   1380
      List            =   "frmInicioSesion.frx":0444
      Style           =   2  'Dropdown List
      TabIndex        =   5
      Top             =   1020
      Width           =   1755
   End
   Begin VB.CommandButton cmdCancelar 
      Caption         =   "&Cancelar"
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
      Left            =   1830
      TabIndex        =   7
      Top             =   1590
      Width           =   1000
   End
   Begin VB.CommandButton cmdAceptar 
      Caption         =   "&Aceptar"
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
      Height          =   300
      Left            =   720
      TabIndex        =   6
      Top             =   1590
      Width           =   1000
   End
   Begin VB.TextBox txtPassword 
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
      IMEMode         =   3  'DISABLE
      Left            =   1380
      MaxLength       =   40
      PasswordChar    =   "*"
      TabIndex        =   3
      Top             =   660
      Width           =   1755
   End
   Begin VB.TextBox txtUsuario 
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
      Left            =   1380
      MaxLength       =   6
      TabIndex        =   1
      Top             =   300
      Width           =   1755
   End
   Begin VB.Label Label1 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "A&mbiente:"
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
      Left            =   555
      TabIndex        =   4
      Top             =   1080
      Width           =   780
   End
   Begin VB.Label lbPassword 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "C&ontraseña:"
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
      Left            =   405
      TabIndex        =   2
      Top             =   705
      Width           =   930
   End
   Begin VB.Label lbUsuario 
      AutoSize        =   -1  'True
      BackStyle       =   0  'Transparent
      Caption         =   "&Usuario:"
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
      Left            =   690
      TabIndex        =   0
      Top             =   345
      Width           =   645
   End
End
Attribute VB_Name = "frmInicioSesion"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
'Public oAccesoDatos As New clsoAccesoDatos

Private Sub cmdAceptar_Click()
    Dim sCadenaCnn As String
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    sAmbiente = UCase(cbAmbiente.Text)
    oAccesoDatos.Conectar (cbAmbiente.Text)
    
    If (oAccesoDatos.EstadoConexion = adStateOpen) Then
        oAccesoDatos.rst.Abrir "select * from PE where CDGEM = 'EMPFIN' AND CODIGO = '" & Me.txtUsuario.Text & "' and CLAVE = CODIFICA('" & Me.txtPassword.Text & "')", oAccesoDatos.cnn.ObjConexion, adOpenDynamic, adLockOptimistic
        
        Select Case oAccesoDatos.rst.HayRegistros
            Case 0  '-----   La consulta no retorno registros.   -----
                MsgBox "El usuario o contraseña no son válidos." & vbNewLine & vbNewLine & "Intente nuevamente o consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oAccesoDatos.rst.Cerrar
                oAccesoDatos.cnn.Cerrar
                txtUsuario.SetFocus
            Case 1  '-----   Hay registros.                       -----
                'MsgBox "Usuario OK!", vbInformation + vbOKOnly, TITULO_MENSAJE
                sUsuarioApp = txtUsuario.Text
                sPasswordApp = txtPassword.Text
                Me.Hide
                frmPrincipal.Show
            Case 2  '-----   El Query no se pudo ejecutar.        -----
                MsgBox "Ocurrio un error al intentar iniciar la sesión." & vbNewLine & vbNewLine & "Intente nuevamente o consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
                oAccesoDatos.rst.Cerrar
                oAccesoDatos.cnn.Cerrar
                txtUsuario.SetFocus
        End Select
'    Else
'        MsgBox "No fue posible abrir la Conexion con la Base de Datos." & vbNewLine & vbNewLine & "Intentelo nuevamente o consulte al administrador de la Aplicación.", vbCritical + vbOKOnly, TITULO_MENSAJE
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub cmdCancelar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    oAccesoDatos.cnn.Cerrar
    oAccesoDatos.rst.Cerrar
    bCerrarApp = True
    Unload Me
    Screen.MousePointer = vbDefault
    
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    'cbAmbiente.AddItem "Prueba"
    cbAmbiente.AddItem "MasConMenos"
    'cbAmbiente.AddItem "Cultiva"
    bCerrarApp = False
    cbAmbiente.ListIndex = 0
    Screen.MousePointer = vbDefault
    Unload Me
    
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Unload(Cancel As Integer)
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If Not bCerrarApp Then Cancel = 1
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub txtPassword_Click()
    txtPassword.SelStart = 0
    txtPassword.SelLength = Len(txtPassword.Text)
End Sub

Private Sub txtPassword_KeyPress(KeyAscii As Integer)
    'KeyAscii = Asc(UCase(Chr(KeyAscii)))
End Sub

Private Sub txtUsuario_Click()
    txtUsuario.SelStart = 0
    txtUsuario.SelLength = Len(txtUsuario.Text)
End Sub

Private Sub txtUsuario_KeyPress(KeyAscii As Integer)
    KeyAscii = Asc(UCase(Chr(KeyAscii)))
End Sub


