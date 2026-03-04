VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmPrincipal 
   AutoRedraw      =   -1  'True
   BackColor       =   &H00FFFFFF&
   Caption         =   "Sistema de administración de microcréditos (Versión 1.0.0)"
   ClientHeight    =   6330
   ClientLeft      =   75
   ClientTop       =   765
   ClientWidth     =   11400
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmPrincipal.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   6330
   ScaleWidth      =   11400
   StartUpPosition =   2  'CenterScreen
   WindowState     =   2  'Maximized
   Begin VB.Timer tmrPrincipal 
      Interval        =   100
      Left            =   1890
      Top             =   2100
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   315
      Left            =   0
      TabIndex        =   0
      Top             =   6015
      Width           =   11400
      _ExtentX        =   20108
      _ExtentY        =   556
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   8
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   7056
            MinWidth        =   7056
            Text            =   "Sistema de administración de microcréditos"
            TextSave        =   "Sistema de administración de microcréditos"
            Key             =   "descripcion"
         EndProperty
         BeginProperty Panel2 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
         EndProperty
         BeginProperty Panel3 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Alignment       =   1
         EndProperty
         BeginProperty Panel4 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Style           =   3
            Alignment       =   2
            Enabled         =   0   'False
            Object.Width           =   1058
            MinWidth        =   1058
            TextSave        =   "INS"
         EndProperty
         BeginProperty Panel5 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Style           =   1
            Alignment       =   2
            Enabled         =   0   'False
            Object.Width           =   1058
            MinWidth        =   1058
            TextSave        =   "CAPS"
         EndProperty
         BeginProperty Panel6 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Style           =   2
            Alignment       =   2
            Object.Width           =   1058
            MinWidth        =   1058
            TextSave        =   "NUM"
         EndProperty
         BeginProperty Panel7 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Alignment       =   1
            Object.Width           =   3175
            MinWidth        =   3175
         EndProperty
         BeginProperty Panel8 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Alignment       =   1
            Object.Width           =   3175
            MinWidth        =   3175
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
   Begin VB.Image ImgFondo 
      Height          =   19200
      Left            =   960
      Picture         =   "frmPrincipal.frx":030A
      Top             =   0
      Width           =   19035
   End
   Begin VB.Menu mnuApliacion 
      Caption         =   "&Sistema"
      Begin VB.Menu mnuSalir 
         Caption         =   "&Salir"
         Shortcut        =   ^S
      End
   End
   Begin VB.Menu mnuHerramientas 
      Caption         =   "&Herramientas"
      Begin VB.Menu mnuAuditorias 
         Caption         =   "&Auditorias"
         Enabled         =   0   'False
         Shortcut        =   ^A
         Visible         =   0   'False
      End
      Begin VB.Menu mnuCredEntSinSaldo 
         Caption         =   "Créditos &Entregados Sin Saldo"
         Shortcut        =   {F2}
         Visible         =   0   'False
      End
      Begin VB.Menu mnuCredLiqConSaldo 
         Caption         =   "Créditos &Liquidados Con Saldo"
         Shortcut        =   {F3}
         Visible         =   0   'False
      End
      Begin VB.Menu mnuIntIncorrectos 
         Caption         =   "Intereses &Incorrectos"
         Shortcut        =   {F4}
         Visible         =   0   'False
      End
      Begin VB.Menu mnuIntNoDist 
         Caption         =   "Intereses &No Distribuidos"
         Shortcut        =   {F5}
         Visible         =   0   'False
      End
      Begin VB.Menu mnuPagosNoDist 
         Caption         =   "&Pagos No Distribuidos"
         Shortcut        =   {F6}
         Visible         =   0   'False
      End
      Begin VB.Menu mnuSeparador1 
         Caption         =   "-"
      End
      Begin VB.Menu mnuMonSistema 
         Caption         =   "&Monitoreo del Sistema"
         Enabled         =   0   'False
         Shortcut        =   {F7}
         Visible         =   0   'False
      End
      Begin VB.Menu mnuSeparador2 
         Caption         =   "-"
         Visible         =   0   'False
      End
      Begin VB.Menu mnuPagos 
         Caption         =   "&Pagos"
         Begin VB.Menu mnuImportación 
            Caption         =   "&Importación de pagos"
            Shortcut        =   ^I
         End
         Begin VB.Menu mnuConciliación 
            Caption         =   "&Conciliación de pagos"
            Shortcut        =   ^C
         End
         Begin VB.Menu mnuBitacora 
            Caption         =   "&Bitácora de importación"
            Shortcut        =   ^B
         End
         Begin VB.Menu mnuSeparador3 
            Caption         =   "-"
         End
         Begin VB.Menu mnuElimPagos 
            Caption         =   "&Eliminación de pagos"
            Shortcut        =   ^E
         End
         Begin VB.Menu mnuConsultaPagos 
            Caption         =   "Consulta de &pagos"
            Shortcut        =   ^P
         End
         Begin VB.Menu mnuRegPagos 
            Caption         =   "&Registro de depósitos"
            Shortcut        =   ^R
         End
         Begin VB.Menu mnuIdenPagos 
            Caption         =   "I&dentifica Pagos"
            Shortcut        =   ^N
         End
      End
      Begin VB.Menu mnuSeparador4 
         Caption         =   "-"
      End
      Begin VB.Menu mnuGarantias 
         Caption         =   "&Garantias"
         Begin VB.Menu mnuDevGar 
            Caption         =   "Devolucion de Garantía"
            Shortcut        =   ^D
         End
      End
      Begin VB.Menu mnuCierre 
         Caption         =   "&Cierre"
         Begin VB.Menu mnuCierreDia 
            Caption         =   "Cierre de Día"
         End
      End
   End
   Begin VB.Menu mnuReportes 
      Caption         =   "&Reportes"
      Begin VB.Menu mnuDesembolsoDiario 
         Caption         =   "&Desembolso Diario"
         Begin VB.Menu mnuPolizaDesemDia 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuDevengo 
         Caption         =   "De&vengo Diario"
         Begin VB.Menu mnuPolizaDevengo 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuCheques 
         Caption         =   "Cheques Diario"
         Begin VB.Menu mnuPolizaChequesDiario 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuPagosD 
         Caption         =   "&Pagos Diario"
         Begin VB.Menu mnuPolizaPagosDiario 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuChequesCanc 
         Caption         =   "Cheques Cancelados"
         Begin VB.Menu mnuPolizaChequesCanc 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuDevolucion 
         Caption         =   "Devolución Parcial/Total"
         Begin VB.Menu mnuPolizaDevolucion 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuAjustes 
         Caption         =   "Ajustes"
         Begin VB.Menu mnuPolizaAjustes 
            Caption         =   "&Póliza"
         End
      End
      Begin VB.Menu mnuSeguros 
         Caption         =   "&Seguros"
         Begin VB.Menu mnuVtaSeg 
            Caption         =   "Venta Seguro"
         End
         Begin VB.Menu mnuCtoSeguro 
            Caption         =   "Cos&to Seguro"
         End
         Begin VB.Menu mnuCancVtaSeguro 
            Caption         =   "Canc. Vta Seguro"
         End
         Begin VB.Menu mnuCancCtoSeguro 
            Caption         =   "Canc. Cto Seguro"
         End
      End
   End
   Begin VB.Menu mnuAyuda 
      Caption         =   "&Ayuda"
   End
End
Attribute VB_Name = "frmPrincipal"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub Form_Load()
    Me.sbBarraEstado.Panels(3).Text = sAmbiente
End Sub

Private Sub Form_Resize()
    With sbBarraEstado
        If (.Panels(1).Width + .Panels(3).Width + .Panels(4).Width + .Panels(5).Width + .Panels(6).Width - 10 + .Panels(7).Width + .Panels(8).Width >= Me.Width) Then
            .Panels(2).Width = 10
        Else
            .Panels(2).Width = Me.Width - (.Panels(1).Width + .Panels(3).Width + .Panels(4).Width + .Panels(5).Width + .Panels(6).Width + .Panels(7).Width + .Panels(8).Width)
        End If
    End With
    
    ImgFondo.Left = Me.Width / 2 - ImgFondo.Width / 2
    ImgFondo.Top = Me.Height / 2 - ImgFondo.Height / 2
End Sub

Private Sub Form_Unload(Cancel As Integer)
    If (Not bCerrarApp) Then Cancel = 1
End Sub

Private Sub mnuAuditorias_Click()
    frmAuditorias.Show 1, Me
End Sub

Private Sub mnuBitacora_Click()
    If ValidaAcceso(sUsuarioApp, 3) = 1 Then
        frmBitacora.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub





Private Sub mnuCierreDia_Click()
    If ValidaAcceso(sUsuarioApp, 10) = 1 Then
        frmCierreDia.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuConciliación_Click()
    If ValidaAcceso(sUsuarioApp, 2) = 1 Then
        frmConciliacion.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuConsultaPagos_Click()
    If ValidaAcceso(sUsuarioApp, 5) = 1 Then
        frmConsultaPagos.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub



Private Sub mnuDevGar_Click()
    If ValidaAcceso(sUsuarioApp, 8) = 1 Then
        frmDevGar.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuElimPagos_Click()
    If ValidaAcceso(sUsuarioApp, 4) = 1 Then
        frmEliminarPagos.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuIdenPagos_Click()
    If ValidaAcceso(sUsuarioApp, 7) = 1 Then
        frmIdenPagos.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuImportación_Click()
    If ValidaAcceso(sUsuarioApp, 1) = 1 Then
        frmImportacion.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaAjustes_Click()
    If ValidaAcceso(sUsuarioApp, 13) = 1 Then
        nPoliza = 7
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaChequesCanc_Click()
    If ValidaAcceso(sUsuarioApp, 13) = 1 Then
        nPoliza = 5
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaChequesDiario_Click()
    If ValidaAcceso(sUsuarioApp, 13) = 1 Then
        nPoliza = 3
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaDesemDia_Click()
    If ValidaAcceso(sUsuarioApp, 11) = 1 Then
        nPoliza = 1
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaDevengo_Click()
    If ValidaAcceso(sUsuarioApp, 12) = 1 Then
        nPoliza = 2
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaDevolucion_Click()
     If ValidaAcceso(sUsuarioApp, 14) = 1 Then
        nPoliza = 6
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuPolizaPagosDiario_Click()
    If ValidaAcceso(sUsuarioApp, 14) = 1 Then
        nPoliza = 4
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuRegPagos_Click()
    If ValidaAcceso(sUsuarioApp, 6) = 1 Then
        bIdentifica = False
        frmRegPagos.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuSalir_Click()
    bCerrarApp = True
    Unload frmInicioSesion
    Unload Me
End Sub

Private Sub mnuVtaSeg_Click()
    If ValidaAcceso(sUsuarioApp, 14) = 1 Then
        nPoliza = 20
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuCtoSeguro_Click()
    If ValidaAcceso(sUsuarioApp, 14) = 1 Then
        nPoliza = 21
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuCancVtaSeguro_Click()
    If ValidaAcceso(sUsuarioApp, 14) = 1 Then
        nPoliza = 22
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub mnuCancCtoSeguro_Click()
    If ValidaAcceso(sUsuarioApp, 14) = 1 Then
        nPoliza = 23
        frmPolizas.Show 1, Me
    Else
        MsgBox "Este usuario no tiene privilegios para esta transacción.", vbInformation
        Exit Sub
    End If
End Sub

Private Sub tmrPrincipal_Timer()
    'Dim dFecha As Date
    
    'dFecha = Date
    
    With sbBarraEstado
        DoEvents
        '.Panels(7).Text = Format(Date, "dddd, dd' de 'MMMM' de 'aaaa")
        .Panels(7).Text = Format(Date, "DD/MMMM/YYYY")
        DoEvents
        .Panels(8).Text = Format(Time, "HH:NN:SS am/pm")
    End With
End Sub
