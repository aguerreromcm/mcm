VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Begin VB.UserControl ctlFiltroConciliacion 
   BackColor       =   &H00FFF9F9&
   ClientHeight    =   2535
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   9900
   ScaleHeight     =   2535
   ScaleWidth      =   9900
   ToolboxBitmap   =   "ctlFiltroConciliacion.ctx":0000
   Begin VB.Frame mrCondiciones 
      BackColor       =   &H00FFF9F9&
      Caption         =   "Valores del filtro:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   2355
      Left            =   2400
      TabIndex        =   29
      Top             =   90
      Width           =   7395
      Begin AdminCred.ctlBoton cmdBuscar 
         Height          =   270
         Left            =   6000
         TabIndex        =   12
         Top             =   1980
         Width           =   1260
         _extentx        =   2223
         _extenty        =   476
      End
      Begin VB.CommandButton cmdBuscar1 
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
         Left            =   6270
         TabIndex        =   13
         Top             =   1950
         Visible         =   0   'False
         Width           =   1000
      End
      Begin VB.TextBox txtCtaBancaria 
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
         Left            =   210
         TabIndex        =   11
         Text            =   "00"
         Top             =   1770
         Width           =   345
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
         ItemData        =   "ctlFiltroConciliacion.ctx":0312
         Left            =   210
         List            =   "ctlFiltroConciliacion.ctx":031C
         Style           =   2  'Dropdown List
         TabIndex        =   1
         Top             =   570
         Width           =   1425
      End
      Begin VB.ComboBox cbTipoCliente 
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
         ItemData        =   "ctlFiltroConciliacion.ctx":0331
         Left            =   3750
         List            =   "ctlFiltroConciliacion.ctx":033E
         Style           =   2  'Dropdown List
         TabIndex        =   5
         Top             =   570
         Width           =   1600
      End
      Begin VB.TextBox txtNombre 
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
         Left            =   6450
         TabIndex        =   9
         Text            =   "Text1"
         Top             =   570
         Width           =   405
      End
      Begin VB.TextBox txtCodigo 
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
         Left            =   5580
         MaxLength       =   6
         TabIndex        =   7
         Text            =   "000000"
         Top             =   570
         Width           =   795
      End
      Begin MSComCtl2.DTPicker DPFechaPago 
         Height          =   300
         Left            =   1890
         TabIndex        =   3
         Top             =   570
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
         Format          =   52297729
         CurrentDate     =   38597
      End
      Begin VB.Label lbEtqCtaBancaria 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Ct&a.:"
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
         Left            =   210
         TabIndex        =   10
         Top             =   1560
         Width           =   375
      End
      Begin VB.Label lbEtqEmpresa 
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
         Left            =   210
         TabIndex        =   0
         Top             =   360
         Width           =   720
      End
      Begin VB.Label lbEtqFechaPago 
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
         Left            =   1890
         TabIndex        =   2
         Top             =   360
         Width           =   1140
      End
      Begin VB.Label lbEtqTipoCliente 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Tipo de cliente:"
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
         Left            =   3750
         TabIndex        =   4
         Top             =   360
         Width           =   1170
      End
      Begin VB.Label lbEtqNombre 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "&Ciclo:"
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
         Left            =   6450
         TabIndex        =   8
         Top             =   360
         Width           =   450
      End
      Begin VB.Label lbEtqCodigo 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Có&digo:"
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
         Left            =   5580
         TabIndex        =   6
         Top             =   360
         Width           =   585
      End
   End
   Begin VB.Frame mrCondFiltrado 
      BackColor       =   &H00FFF9F9&
      Caption         =   "Tipo de filtro:"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   6.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   2355
      Left            =   120
      TabIndex        =   28
      Top             =   90
      Width           =   2205
      Begin AdminCred.ctlBoton cmdSinFiltro 
         Height          =   270
         Left            =   450
         TabIndex        =   14
         Top             =   1950
         Width           =   1260
         _extentx        =   2223
         _extenty        =   476
      End
      Begin VB.PictureBox pbCtaBancaria 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   240
         Picture         =   "ctlFiltroConciliacion.ctx":035F
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   30
         Top             =   1620
         Width           =   210
      End
      Begin VB.CommandButton cmdSinFiltro1 
         Caption         =   "&Quitar filtro"
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
         Left            =   270
         TabIndex        =   15
         Top             =   1950
         Visible         =   0   'False
         Width           =   1600
      End
      Begin VB.PictureBox pbNombreCliente 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   240
         Picture         =   "ctlFiltroConciliacion.ctx":0551
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   25
         Top             =   1350
         Width           =   210
      End
      Begin VB.PictureBox pbCodigo 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   240
         Picture         =   "ctlFiltroConciliacion.ctx":0743
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   23
         Top             =   1080
         Width           =   210
      End
      Begin VB.PictureBox pbTipoCliente 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   240
         Picture         =   "ctlFiltroConciliacion.ctx":0935
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   21
         Top             =   810
         Width           =   210
      End
      Begin VB.PictureBox pbFechaPago 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   240
         Picture         =   "ctlFiltroConciliacion.ctx":0B27
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   19
         Top             =   540
         Width           =   210
      End
      Begin VB.PictureBox pbEmpresa 
         Appearance      =   0  'Flat
         AutoSize        =   -1  'True
         BackColor       =   &H80000005&
         ForeColor       =   &H80000008&
         Height          =   210
         Left            =   240
         Picture         =   "ctlFiltroConciliacion.ctx":0D19
         ScaleHeight     =   180
         ScaleWidth      =   180
         TabIndex        =   17
         Top             =   270
         Width           =   210
      End
      Begin VB.Label lbCtaBancaria 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Cta. bancaria"
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
         Left            =   540
         TabIndex        =   31
         Top             =   1635
         Width           =   1005
      End
      Begin VB.Label lbNombre 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Ciclo"
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
         Left            =   540
         TabIndex        =   24
         Top             =   1365
         Width           =   390
      End
      Begin VB.Label lbCodigo 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Código"
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
         Left            =   540
         TabIndex        =   22
         Top             =   1095
         Width           =   525
      End
      Begin VB.Label lbTipoCliente 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Tipo de cliente"
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
         Left            =   540
         TabIndex        =   20
         Top             =   825
         Width           =   1110
      End
      Begin VB.Label lbFechaPago 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Fecha de pago"
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
         Left            =   540
         TabIndex        =   18
         Top             =   555
         Width           =   1080
      End
      Begin VB.Label lbEmpresa 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Empresa"
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
         Left            =   540
         TabIndex        =   16
         Top             =   285
         Width           =   660
      End
   End
   Begin VB.PictureBox pbNO 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   4110
      Picture         =   "ctlFiltroConciliacion.ctx":0F0B
      ScaleHeight     =   180
      ScaleWidth      =   180
      TabIndex        =   27
      Top             =   3630
      Visible         =   0   'False
      Width           =   210
   End
   Begin VB.PictureBox pbOK 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   3780
      Picture         =   "ctlFiltroConciliacion.ctx":10FD
      ScaleHeight     =   180
      ScaleWidth      =   180
      TabIndex        =   26
      Top             =   3630
      Visible         =   0   'False
      Width           =   210
   End
End
Attribute VB_Name = "ctlFiltroConciliacion"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Public Event ClickEmpresa()
Public Event ClickFechaPago()
Public Event ClickTipoCliente()
Public Event ClickCtaBancaria()
Public Event ClickCodigo()
Public Event ClickNombre()
Public Event ClickSinFiltro()
Public Event ClickBuscar()

Private msEmpresa As String
Private msFechaPago As String
Private msTipoCliente As String
Private msCodigo As String
Private msNombre As String
Private msCtaBancaria As String

Private mbOptEmpresa As Boolean
Private mbOptFechaPago As Boolean
Private mbOptTipoCliente As Boolean
Private mbOptCodigo As Boolean
Private mbOptNombre As Boolean
Private mbOptCtaBancaria As Boolean

Private mbHabilitado As Boolean
Private mbQuitarFiltro As Boolean

Public Property Get QuitarFiltro() As Boolean
    QuitarFiltro = mbQuitarFiltro
End Property

Public Property Let QuitarFiltro(pbValor As Boolean)
    mbQuitarFiltro = pbValor
    UserControl.cmdSinFiltro.Habilitado = mbQuitarFiltro
End Property

Public Property Get Habilitado() As Boolean
    Habilitado = mbHabilitado
End Property

Public Property Let Habilitado(pbValor As Boolean)
    With UserControl
        mbHabilitado = pbValor
        UserControl.Enabled = pbValor
        
        If (pbValor) Then
            .pbCodigo.Enabled = True
            .pbCtaBancaria.Enabled = True
            .pbEmpresa.Enabled = True
            .pbFechaPago.Enabled = True
            .pbNombreCliente.Enabled = True
            .pbTipoCliente.Enabled = True
            .lbCodigo.Enabled = True
            .lbCtaBancaria.Enabled = True
            .lbEmpresa.Enabled = True
            .lbFechaPago.Enabled = True
            .lbNombre.Enabled = True
            .lbTipoCliente.Enabled = True
            .mrCondFiltrado.Enabled = True
            .mrCondiciones.Enabled = True
            .cmdBuscar.Habilitado = True
            .cmdSinFiltro.Habilitado = True
        Else
            .pbCodigo.Enabled = False
            .pbCtaBancaria.Enabled = False
            .pbEmpresa.Enabled = False
            .pbFechaPago.Enabled = False
            .pbNombreCliente.Enabled = False
            .pbTipoCliente.Enabled = False
            .lbCodigo.Enabled = False
            .lbCtaBancaria.Enabled = False
            .lbEmpresa.Enabled = False
            .lbFechaPago.Enabled = False
            .lbNombre.Enabled = False
            .lbTipoCliente.Enabled = False
            .mrCondFiltrado.Enabled = False
            .mrCondiciones.Enabled = False
            .cmdBuscar.Habilitado = False
            .cmdSinFiltro.Habilitado = False
        End If
    End With
End Property

Public Property Get Empresa() As String
    Empresa = msEmpresa
End Property

Public Property Get OptEmpresa() As Boolean
    OptEmpresa = mbOptEmpresa
End Property

Public Property Let OptEmpresa(ByVal pbValor As Boolean)
    mbOptEmpresa = pbValor
    
    If (pbValor) Then
        Set pbEmpresa.Picture = pbOK.Picture
        cbEmpresa.Enabled = True
        lbEtqEmpresa.Enabled = True
    Else
        Set pbEmpresa.Picture = pbNO.Picture
        cbEmpresa.Enabled = False
        lbEtqEmpresa.Enabled = False
    End If
End Property

Public Property Get FechaPago() As String
    FechaPago = msFechaPago
End Property

Public Property Get OptFechaPago() As Boolean
    OptFechaPago = mbOptFechaPago
End Property

Public Property Let OptFechaPago(ByVal pbValor As Boolean)
    mbOptFechaPago = pbValor
    
    If (pbValor) Then
        Set pbFechaPago.Picture = pbOK.Picture
        DPFechaPago.Enabled = True
        lbEtqFechaPago.Enabled = True
        msFechaPago = DPFechaPago.Value
    Else
        Set pbFechaPago.Picture = pbNO.Picture
        DPFechaPago.Enabled = False
        lbEtqFechaPago.Enabled = False
    End If
End Property

Public Property Get TipoCliente() As String
    TipoCliente = msTipoCliente
End Property

Public Property Get OptTipoCliente() As Boolean
    OptTipoCliente = mbOptTipoCliente
End Property

Public Property Let OptTipoCliente(ByVal pbValor As Boolean)
    mbOptTipoCliente = pbValor
    
    If (pbValor) Then
        Set pbTipoCliente.Picture = pbOK.Picture
        cbTipoCliente.Enabled = True
        lbEtqTipoCliente.Enabled = True
    Else
        Set pbTipoCliente.Picture = pbNO.Picture
        cbTipoCliente.Enabled = False
        lbEtqTipoCliente.Enabled = False
    End If
End Property

Public Property Get Codigo() As String
    Codigo = msCodigo
End Property

Public Property Get OptCodigo() As Boolean
    OptCodigo = mbOptCodigo
End Property

Public Property Let OptCodigo(ByVal pbValor As Boolean)
    mbOptCodigo = pbValor
    
    If (pbValor) Then
        Set pbCodigo.Picture = pbOK.Picture
        txtCodigo.Enabled = True
        lbEtqCodigo.Enabled = True
    Else
        Set pbCodigo.Picture = pbNO.Picture
        txtCodigo.Enabled = False
        lbEtqCodigo.Enabled = False
    End If
End Property

Public Property Get Nombre() As String
    Nombre = msNombre
End Property

Public Property Get OptNombre() As Boolean
    OptNombre = mbOptNombre
End Property

Public Property Let OptNombre(ByVal pbValor As Boolean)
    mbOptNombre = pbValor
    
    If (pbValor) Then
        Set pbNombreCliente.Picture = pbOK.Picture
        txtNombre.Enabled = True
        lbEtqNombre.Enabled = True
    Else
        Set pbNombreCliente.Picture = pbNO.Picture
        txtNombre.Enabled = False
        lbEtqNombre.Enabled = False
    End If
End Property

Public Property Get CtaBancaria() As String
    CtaBancaria = msCtaBancaria
End Property

Public Property Get OptCtaBancaria() As Boolean
    OptCtaBancaria = mbOptCtaBancaria
End Property

Public Property Let OptCtaBancaria(ByVal pbValor As Boolean)
    mbOptCtaBancaria = pbValor
    
    If (pbValor) Then
        Set pbCtaBancaria.Picture = pbOK.Picture
        txtCtaBancaria.Enabled = True
        lbEtqCtaBancaria.Enabled = True
    Else
        Set pbCtaBancaria.Picture = pbNO.Picture
        txtCtaBancaria.Enabled = False
        lbEtqCtaBancaria.Enabled = False
    End If
End Property

Private Sub cbEmpresa_Click()
    msEmpresa = cbEmpresa.Text
End Sub

Private Sub cbTipoCliente_Click()
    msTipoCliente = cbTipoCliente.Text
End Sub

Private Sub cmdBuscar_Click()
    'RaiseEvent ClickBuscar
End Sub

Private Sub cmdBuscar_BotonClick()
    RaiseEvent ClickBuscar
End Sub

Private Sub cmdSinFiltro_BotonClick()
    Call UserControl_Initialize
    RaiseEvent ClickSinFiltro
End Sub

Private Sub DPFechaPago_Change()
    msFechaPago = DPFechaPago.Value
End Sub

Private Sub txtCodigo_Change()
    msCodigo = txtCodigo.Text
End Sub

Private Sub txtCodigo_Click()
    txtCodigo.SelStart = 0
    txtCodigo.SelLength = Len(txtCodigo.Text)
End Sub

Private Sub txtCodigo_KeyPress(KeyAscii As Integer)
    If ((KeyAscii < 48 Or KeyAscii > 57) And KeyAscii <> 8) Then
        KeyAscii = 0
    End If
End Sub

Private Sub txtCtaBancaria_Change()
    msCtaBancaria = txtCtaBancaria.Text
End Sub

Private Sub txtCtaBancaria_Click()
    txtCtaBancaria.SelStart = 0
    txtCtaBancaria.SelLength = Len(txtCtaBancaria.Text)
End Sub

Private Sub txtCtaBancaria_KeyPress(KeyAscii As Integer)
    If ((KeyAscii < 48 Or KeyAscii > 57) And KeyAscii <> 8) Then
        KeyAscii = 0
    End If
End Sub

Private Sub txtNombre_Change()
    msNombre = txtNombre.Text
End Sub

Private Sub cmdSinFiltro_Click()
'    Call UserControl_Initialize
'    RaiseEvent ClickSinFiltro
End Sub

Private Sub lbCodigo_Click()
    Call pbCodigo_Click
End Sub

Private Sub lbCtaBancaria_Click()
    Call pbCtaBancaria_Click
End Sub

Private Sub lbEmpresa_Click()
    Call pbEmpresa_Click
End Sub

Private Sub lbFechaPago_Click()
    Call pbFechaPago_Click
End Sub

Private Sub lbNombre_Click()
    Call pbNombreCliente_Click
End Sub

Private Sub lbTipoCliente_Click()
    Call pbTipoCliente_Click
End Sub

Private Sub pbCodigo_Click()
    With UserControl
        If .pbCodigo.Picture = .pbNO.Picture Then
            Set .pbCodigo.Picture = .pbOK.Picture
            .txtCodigo.Text = ""
            .txtCodigo.Enabled = True
            .lbEtqCodigo.Enabled = True
            mbOptCodigo = True
        Else
            Set .pbCodigo.Picture = .pbNO.Picture
            .txtCodigo.Text = ""
            .txtCodigo.Enabled = False
            .lbEtqCodigo.Enabled = False
            mbOptCodigo = False
        End If
        
        If .pbNombreCliente.Picture = .pbOK.Picture Then
            Set .pbNombreCliente.Picture = .pbNO.Picture
            .txtNombre.Text = ""
            .txtNombre.Enabled = False
            .lbEtqNombre.Enabled = False
            mbOptNombre = False
        End If
        
        RaiseEvent ClickCodigo
    End With
End Sub

Private Sub pbCtaBancaria_Click()
    With UserControl
        If .pbCtaBancaria.Picture = .pbNO.Picture Then
            Set .pbCtaBancaria.Picture = .pbOK.Picture
            .txtCtaBancaria.Text = ""
            .txtCtaBancaria.Enabled = True
            .lbEtqCtaBancaria.Enabled = True
            mbOptCtaBancaria = True
        Else
            Set .pbCtaBancaria.Picture = .pbNO.Picture
            .txtCtaBancaria.Text = ""
            .txtCtaBancaria.Enabled = False
            .lbEtqCtaBancaria.Enabled = False
            mbOptCtaBancaria = False
        End If
        
        RaiseEvent ClickCtaBancaria
    End With
End Sub

Private Sub pbEmpresa_Click()
    With UserControl
        If .pbEmpresa.Picture = .pbNO.Picture Then
            Set .pbEmpresa.Picture = .pbOK.Picture

            .cbEmpresa.Enabled = True
            .lbEtqEmpresa.Enabled = True
            mbOptEmpresa = True
        Else
            Set .pbEmpresa.Picture = .pbNO.Picture
            
            .cbEmpresa.Enabled = False
            .lbEtqEmpresa.Enabled = False
            mbOptEmpresa = False
        End If
        
        .cbEmpresa.ListIndex = 0
        RaiseEvent ClickEmpresa
    End With
End Sub

Private Sub pbFechaPago_Click()
    With UserControl
        If .pbFechaPago.Picture = .pbNO.Picture Then
            Set .pbFechaPago.Picture = .pbOK.Picture
            .DPFechaPago.Value = Date
            .DPFechaPago.Enabled = True
            .lbEtqFechaPago.Enabled = True
            mbOptFechaPago = True
            'msFechaPago = ""
        Else
            Set .pbFechaPago.Picture = .pbNO.Picture
            .DPFechaPago.Value = Date
            .DPFechaPago.Enabled = False
            .lbEtqFechaPago.Enabled = False
            mbOptFechaPago = False
            msFechaPago = ""
        End If
        
        RaiseEvent ClickFechaPago
    End With
End Sub

Private Sub pbNombreCliente_Click()
    With UserControl
        If .pbNombreCliente.Picture = .pbNO.Picture Then
            Set .pbNombreCliente.Picture = .pbOK.Picture
            .txtNombre.Text = ""
            .txtNombre.Enabled = True
            .lbEtqNombre.Enabled = True
            mbOptNombre = True
        Else
            Set .pbNombreCliente.Picture = .pbNO.Picture
            .txtNombre.Text = ""
            .txtNombre.Enabled = False
            .lbEtqNombre.Enabled = False
            mbOptNombre = False
        End If
        
        If .pbCodigo.Picture = .pbOK.Picture Then
            Set .pbCodigo.Picture = .pbNO.Picture
            .txtCodigo.Text = ""
            .txtCodigo.Enabled = False
            .lbEtqCodigo.Enabled = False
            mbOptCodigo = False
        End If
        
        RaiseEvent ClickNombre
    End With
End Sub

Private Sub pbTipoCliente_Click()
    With UserControl
        If .pbTipoCliente.Picture = .pbNO.Picture Then
            Set .pbTipoCliente.Picture = .pbOK.Picture

            .cbTipoCliente.Enabled = True
            .lbEtqTipoCliente.Enabled = True
            mbOptTipoCliente = True
        Else
            Set .pbTipoCliente.Picture = .pbNO.Picture
            
            .cbTipoCliente.Enabled = False
            .lbEtqTipoCliente.Enabled = False
            mbOptTipoCliente = False
        End If
        
        .cbTipoCliente.ListIndex = 0
        RaiseEvent ClickTipoCliente
    End With
End Sub

Private Sub txtNombre_Click()
    txtNombre.SelStart = 0
    txtNombre.SelLength = Len(txtNombre.Text)
End Sub

Private Sub txtNombre_KeyPress(KeyAscii As Integer)
   KeyAscii = Asc(UCase(Chr(KeyAscii)))
End Sub

Private Sub UserControl_Initialize()
    With UserControl
        Set .pbEmpresa.Picture = .pbNO.Picture
        Set .pbFechaPago.Picture = .pbNO.Picture
        Set .pbTipoCliente.Picture = .pbNO.Picture
        Set .pbCodigo.Picture = .pbNO.Picture
        Set .pbNombreCliente.Picture = .pbNO.Picture
        Set .pbCtaBancaria.Picture = .pbNO.Picture
        .cbEmpresa.ListIndex = 0
        .DPFechaPago.Value = Date
        .cbTipoCliente.ListIndex = 0
        .txtCodigo.Text = ""
        .txtNombre.Text = ""
        .txtCtaBancaria.Text = ""
        .lbEtqCodigo.Enabled = False
        .lbEtqEmpresa.Enabled = False
        .lbEtqFechaPago.Enabled = False
        .lbEtqCtaBancaria.Enabled = False
        .lbEtqNombre.Enabled = False
        .lbEtqTipoCliente.Enabled = False
        .cbEmpresa.Enabled = False
        .DPFechaPago.Enabled = False
        .cbTipoCliente.Enabled = False
        .txtCtaBancaria.Enabled = False
        .txtCodigo.Enabled = False
        .txtNombre.Enabled = False
        mbOptEmpresa = False
        mbOptFechaPago = False
        mbOptTipoCliente = False
        mbOptCodigo = False
        mbOptNombre = False
        mbOptCtaBancaria = False
        mbHabilitado = True
        msFechaPago = DPFechaPago.Value
        cmdBuscar.Texto = "&Buscar"
        cmdBuscar.Habilitado = True
        cmdSinFiltro.Texto = "&Quitar filtro"
        cmdSinFiltro.Habilitado = True
    End With
End Sub

Private Sub UserControl_Resize()
    UserControl.Height = 2530
    UserControl.Width = 9900
End Sub
