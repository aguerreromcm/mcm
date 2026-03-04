VERSION 5.00
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Object = "{BDC217C8-ED16-11CD-956C-0000C04E4C0A}#1.1#0"; "TABCTL32.OCX"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmEliminarPagos 
   BackColor       =   &H00FFF9F9&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de Eliminación de Pagos"
   ClientHeight    =   10965
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   9900
   Icon            =   "frmEliminarPagos.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   10965
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
      Picture         =   "frmEliminarPagos.frx":030A
      ScaleHeight     =   195
      ScaleWidth      =   225
      TabIndex        =   27
      Top             =   10320
      Visible         =   0   'False
      Width           =   225
   End
   Begin AdminCred.ctlFiltroConciliacion ctlFiltroConciliacion1 
      Height          =   2535
      Left            =   0
      TabIndex        =   18
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
      Left            =   8850
      TabIndex        =   9
      Top             =   10290
      Width           =   1000
   End
   Begin VB.CommandButton cmdEliminacion 
      Caption         =   "Eliminar &pagos..."
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
      TabIndex        =   8
      Top             =   10290
      Width           =   1600
   End
   Begin VB.PictureBox pbSel 
      Appearance      =   0  'Flat
      AutoSize        =   -1  'True
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   210
      Left            =   2130
      Picture         =   "frmEliminarPagos.frx":05BC
      ScaleHeight     =   210
      ScaleWidth      =   210
      TabIndex        =   7
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
      Picture         =   "frmEliminarPagos.frx":0866
      ScaleHeight     =   210
      ScaleWidth      =   210
      TabIndex        =   6
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
         Picture         =   "frmEliminarPagos.frx":0B10
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   28
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
         Caption         =   "Módulo de Eliminación de Pagos"
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
         Width           =   4665
      End
   End
   Begin MSComctlLib.ProgressBar pbarElimPagos 
      Height          =   195
      Left            =   5100
      TabIndex        =   4
      Top             =   10740
      Width           =   2265
      _ExtentX        =   3995
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   5
      Top             =   10680
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
      Height          =   6855
      Left            =   30
      TabIndex        =   10
      Top             =   3330
      Width           =   9825
      _ExtentX        =   17330
      _ExtentY        =   12091
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
      TabCaption(0)   =   "Lista de pagos a eliminar"
      TabPicture(0)   =   "frmEliminarPagos.frx":10E5
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
      Tab(0).Control(5)=   "Label2"
      Tab(0).Control(5).Enabled=   0   'False
      Tab(0).Control(6)=   "Label3"
      Tab(0).Control(6).Enabled=   0   'False
      Tab(0).Control(7)=   "lbMontoNoIden"
      Tab(0).Control(7).Enabled=   0   'False
      Tab(0).Control(8)=   "lbNoRegsNoIden"
      Tab(0).Control(8).Enabled=   0   'False
      Tab(0).Control(9)=   "Label9"
      Tab(0).Control(9).Enabled=   0   'False
      Tab(0).Control(10)=   "Label5"
      Tab(0).Control(10).Enabled=   0   'False
      Tab(0).Control(11)=   "fgNoIdentificados"
      Tab(0).Control(11).Enabled=   0   'False
      Tab(0).Control(12)=   "fgIdentificados"
      Tab(0).Control(12).Enabled=   0   'False
      Tab(0).Control(13)=   "cmdQuitarSel"
      Tab(0).Control(13).Enabled=   0   'False
      Tab(0).Control(14)=   "cmdSelTodos"
      Tab(0).Control(14).Enabled=   0   'False
      Tab(0).ControlCount=   15
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
         Left            =   6330
         TabIndex        =   12
         Top             =   6480
         Visible         =   0   'False
         Width           =   1700
      End
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
         Left            =   8070
         TabIndex        =   11
         Top             =   6480
         Visible         =   0   'False
         Width           =   1700
      End
      Begin MSFlexGridLib.MSFlexGrid fgIdentificados 
         Height          =   2715
         Left            =   60
         TabIndex        =   13
         Top             =   720
         Width           =   9735
         _ExtentX        =   17171
         _ExtentY        =   4789
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
      Begin MSFlexGridLib.MSFlexGrid fgNoIdentificados 
         Height          =   2355
         Left            =   60
         TabIndex        =   21
         Top             =   4080
         Width           =   9735
         _ExtentX        =   17171
         _ExtentY        =   4154
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
         BackColor       =   &H00008000&
         BorderStyle     =   1  'Fixed Single
         Caption         =   "Pagos Identificados (MP)"
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
         TabIndex        =   26
         Top             =   480
         Width           =   9705
      End
      Begin VB.Label Label9 
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
         TabIndex        =   25
         Top             =   6480
         Width           =   1260
      End
      Begin VB.Label lbNoRegsNoIden 
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
         ForeColor       =   &H00FF7070&
         Height          =   195
         Left            =   1380
         TabIndex        =   24
         Top             =   6480
         Width           =   120
      End
      Begin VB.Label lbMontoNoIden 
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
         ForeColor       =   &H00FF7070&
         Height          =   195
         Left            =   3585
         TabIndex        =   23
         Top             =   6480
         Width           =   540
      End
      Begin VB.Label Label3 
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
         TabIndex        =   22
         Top             =   6480
         Width           =   525
      End
      Begin VB.Label Label2 
         Alignment       =   2  'Center
         BackColor       =   &H00FF7070&
         BorderStyle     =   1  'Fixed Single
         Caption         =   "Garantía Liquida"
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
         TabIndex        =   20
         Top             =   3840
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
         TabIndex        =   19
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
         TabIndex        =   17
         Top             =   3480
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
         ForeColor       =   &H00008000&
         Height          =   195
         Left            =   3585
         TabIndex        =   16
         Top             =   3480
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
         ForeColor       =   &H00008000&
         Height          =   195
         Left            =   1380
         TabIndex        =   15
         Top             =   3480
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
         TabIndex        =   14
         Top             =   3480
         Width           =   1260
      End
   End
End
Attribute VB_Name = "frmEliminarPagos"
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

Private Sub cmdEliminacion_Click()
    Dim lRenglon As Long, sCadenaSQL As String
    Dim oRstObtPago As New clsoAdoRecordset
    Dim vColoFrente As Variant, vColorFondo As Variant
    Dim respuesta As Variant
    Dim sCdgNS As String, lNoIdenEliminar As Long, lIdenEliminar As Long, lNoPagoNoIden As Long, lNoPagoIden As Long
    Dim acmd As New ADODB.Command
    Dim Mensaje As String
        
    'On Error GoTo RutinaError
    respuesta = MsgBox("Esta a punto de eliminar toda la información relacionada con los pagos seleccionados." & vbNewLine & "¿Esta seguro(a) que desea eliminar de los pagos?", vbYesNo + vbQuestion, TITULO_MENSAJE)
    If (respuesta = vbNo) Then
        Exit Sub
    End If
    Screen.MousePointer = vbHourglass
    
    DoEvents
    'cmdExpExcel.Visible = False
    cmdEliminacion.Visible = False
    cmdSelTodos.Visible = False
    cmdQuitarSel.Visible = False
    cmdCerrar.Enabled = False
    ctlFiltroConciliacion1.Habilitado = False
    
    With fgIdentificados
        lIdenEliminar = 0
        
        For lRenglon = 1 To .Rows - 1
            .Row = lRenglon
            .Col = 1
            If (.CellPicture = pbSel.Picture) Then
                lIdenEliminar = lIdenEliminar + 1
            End If
        Next
        
        If (lIdenEliminar > 0) Then
            pbarElimPagos.Value = 0
            pbarElimPagos.Max = lIdenEliminar
            pbarElimPagos.Visible = True
            lNoPagoIden = 0
            
            For lRenglon = 1 To .Rows - 1
                .Row = lRenglon
                .Col = 1
                DoEvents
                
                If (.CellPicture = Me.pbSel) Then
                    pbarElimPagos.Value = lNoPagoIden
                    sbBarraEstado.Panels(1).Text = "Eliminando pago (Identificado) no. " & CStr(lNoPagoIden) & " de " & CStr(lIdenEliminar) & "  (" & CStr(Format(((lNoPagoIden) * 100) / lIdenEliminar, "##0.00")) & "%)"
                
                    '-----   Ejecutamos el proceso de Conciliación para el pago en cuestión   -----
                    If (Mid(.TextMatrix(lRenglon, 6), 1, 1) = "G") Then sCdgNS = "'" & .TextMatrix(lRenglon, 7) & "'" Else sCdgNS = "null"
                    'sCadenaSQL = "eliminar_pagos_mp('" & .TextMatrix(lRenglon, 3) & "', " & sCdgNS & ", '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "', '" & .TextMatrix(lRenglon, 7) & "', '" & .TextMatrix(lRenglon, 13) & "', '" & Mid(.TextMatrix(lRenglon, 6), 1, 1) & "', '" & .TextMatrix(lRenglon, 8) & "', '" & .TextMatrix(lRenglon, 10) & "', " & Replace(Replace(.TextMatrix(lRenglon, 12), "$", ""), ",", "") & ", '" & sUsuarioApp & "', '" & .TextMatrix(lRenglon, 14) & "', null, 0)"
                    'oAccesoDatos.cnn.Ejecutar sCadenaSQL
                    'lNoPagoIden = lNoPagoIden + 1
                    
                    Set acmd = Nothing

                        acmd.CommandText = "spEliminaImportacionEMPFIN"
                        acmd.CommandType = adCmdStoredProc
                        acmd.ActiveConnection = oAccesoDatos.cnn.ObjConexion

                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'Empresa
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'CDGCLNS
                        acmd.Parameters.Append acmd.CreateParameter(, adNumeric, adParamInput, 30)  'TIPO (Pago o Garantia)
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'CLNS
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'CICLO
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'SECUENCIAMP
                        acmd.Parameters.Append acmd.CreateParameter(, adDate, adParamInput, 30)     'Fecha de Pago
                        acmd.Parameters.Append acmd.CreateParameter(, adNumeric, adParamInput, 30)  'Monto
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'Usuario
                        acmd.Parameters.Append acmd.CreateParameter(, adNumeric, adParamInput, 30)  'SecuenciaGar
            
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'Cuenta
                        
                        acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamOutput, 200)  'Resultado de la ejecución del SP

                        acmd.Parameters(0) = .TextMatrix(lRenglon, 3)
                        acmd.Parameters(1) = .TextMatrix(lRenglon, 7)
                        acmd.Parameters(2) = 0
                        acmd.Parameters(3) = Mid(.TextMatrix(lRenglon, 6), 1, 1)
                        acmd.Parameters(4) = .TextMatrix(lRenglon, 8)
                        acmd.Parameters(5) = .TextMatrix(lRenglon, 14)
                        acmd.Parameters(6) = Format(.TextMatrix(lRenglon, 4), "YYYY/MM/DD")
                        acmd.Parameters(7) = Replace(Replace(.TextMatrix(lRenglon, 12), "$", ""), ",", "")
                        acmd.Parameters(8) = sUsuarioApp
                        acmd.Parameters(9) = 0
                        acmd.Parameters(10) = Null
                    
                    acmd.Execute
                    'MsgBox "Resultado = " & acmd.Parameters(9)

                    If Mid(acmd.Parameters(11), 1, 1) <> "1" Then
                    'If "1" <> "1" Then
                        Mensaje = ""
                        Mensaje = Mensaje & "Error al eliminar el pago del día " & .TextMatrix(lRenglon, 4) & " por un importe de " & .TextMatrix(lRenglon, 12) & vbNewLine
                        Mensaje = Mensaje & "Favor de verificar con el administrador del sistema." & vbNewLine
                        Screen.MousePointer = vbDefault
                    
                        MsgBox Mensaje, vbInformation + vbOKOnly, "Eliminación de Pagos"
                        
                        If lNoPagoIden > 0 Then
                            lNoPagoIden = lNoPagoIden - 1
                        End If
                    Else
                        lNoPagoIden = lNoPagoIden + 1
                    End If
                    
                    
                End If
            Next
        End If
    End With
    
    With fgNoIdentificados
        lNoIdenEliminar = 0
        
        For lRenglon = 1 To .Rows - 1
            .Row = lRenglon
            .Col = 1
            If (.CellPicture = pbSel.Picture) Then
                lNoIdenEliminar = lNoIdenEliminar + 1
            End If
        Next
    
        If (lNoIdenEliminar > 0) Then
            pbarElimPagos.Value = 0
            pbarElimPagos.Max = lNoIdenEliminar
            pbarElimPagos.Visible = True
            lNoPagoNoIden = 0
        
            For lRenglon = 1 To .Rows - 1
                .Row = lRenglon
                .Col = 1
                DoEvents
                
                If (.CellPicture = Me.pbSel) Then
                    pbarElimPagos.Value = lNoPagoNoIden
                    sbBarraEstado.Panels(1).Text = "Eliminando pago (No Identificado) no. " & CStr(lNoPagoNoIden) & " de " & CStr(lNoIdenEliminar) & "  (" & CStr(Format(((lNoPagoNoIden) * 100) / lNoIdenEliminar, "##0.00")) & "%)"
                
                    '-----   Ejecutamos el proceso de Conciliación para el pago en cuestión   -----
                    'sCadenaSQL = "eliminar_pagos_mp('" & .TextMatrix(lRenglon, 3) & "', null, '" & Format(.TextMatrix(lRenglon, 4), "yyyy/mm/dd") & "', '" & Mid(.TextMatrix(lRenglon, 5), 2, 6) & "', '" & .TextMatrix(lRenglon, 8) & "', '" & IIf(Mid(.TextMatrix(lRenglon, 5), 1, 1) = "0", "G", "I") & "', '" & Mid(.TextMatrix(lRenglon, 5), 8, 2) & "', '" & .TextMatrix(lRenglon, 10) & "', " & Replace(Replace(.TextMatrix(lRenglon, 7), "$", ""), ",", "") & ", '" & sUsuarioApp & "', null, '" & .TextMatrix(lRenglon, 9) & "', 1)"
                    'oAccesoDatos.cnn.Ejecutar sCadenaSQL
                    'lNoPagoNoIden = lNoPagoNoIden + 1
                    
'CREATE OR REPLACE procedure eliminar_pagos_mp(pcdgem     in mp.cdgem%type,     pcdgns    in mp.cdgns%type,
'                                              pfechareal in date,              pcdgclns  in mp.cdgclns%type,
'                                              pcdgcb     in cb.codigo%type,    pclns     in mp.clns%type,
'                                              pciclo     in mp.ciclo%type,     psecueim  in mp.secuenciaim%type,
'                                              pcantidad  in mp.cantidad%type,  pusuario  in mp.actualizarpe%type,
'                                              psecuemp   in mp.secuencia%type, psecuepdi in pdi.secuencia%type,
'                                              ptipo      in number)
                    
                    Set acmd = Nothing

                    acmd.CommandText = "spEliminaImportacionEMPFIN"
                    acmd.CommandType = adCmdStoredProc
                    acmd.ActiveConnection = oAccesoDatos.cnn.ObjConexion

                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'Empresa
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'CDGCLNS
                    acmd.Parameters.Append acmd.CreateParameter(, adNumeric, adParamInput, 30)  'TIPO (Pago o Garantia)
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'CLNS
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'CICLO
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'SECUENCIAMP
                    acmd.Parameters.Append acmd.CreateParameter(, adDate, adParamInput, 30)     'Fecha de Pago
                    acmd.Parameters.Append acmd.CreateParameter(, adNumeric, adParamInput, 30)  'Monto
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'Usuario
                    acmd.Parameters.Append acmd.CreateParameter(, adNumeric, adParamInput, 30)  'SecuenciaGar
                                    
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamInput, 30)  'Cuenta
                        
                    acmd.Parameters.Append acmd.CreateParameter(, adVarChar, adParamOutput, 200)  'Resultado de la ejecución del SP

                    
                    acmd.Parameters(0) = .TextMatrix(lRenglon, 3)
                    acmd.Parameters(1) = Mid(.TextMatrix(lRenglon, 5), 2, 6)
                    acmd.Parameters(2) = 1
                    acmd.Parameters(3) = IIf(Mid(.TextMatrix(lRenglon, 5), 1, 1) = "P" Or Mid(.TextMatrix(lRenglon, 5), 1, 1) = "0", "G", "I")
                    acmd.Parameters(4) = ctlFiltroConciliacion1.Nombre
                    acmd.Parameters(5) = 0
                    acmd.Parameters(6) = Format(.TextMatrix(lRenglon, 4), "YYYY/MM/DD")
                    acmd.Parameters(7) = Replace(Replace(.TextMatrix(lRenglon, 7), "$", ""), ",", "")
                    acmd.Parameters(8) = sUsuarioApp
                    acmd.Parameters(9) = .TextMatrix(lRenglon, 9)
                    acmd.Parameters(10) = .TextMatrix(lRenglon, 8)

                    acmd.Execute
                    'MsgBox "Resultado = " & acmd.Parameters(9)

                    If Mid(acmd.Parameters(11), 1, 1) <> "1" Then
                        Mensaje = ""
                        Mensaje = Mensaje & "Error al eliminar el pago del día " & .TextMatrix(lRenglon, 4) & " por un importe de " & .TextMatrix(lRenglon, 7) & vbNewLine & vbNewLine
                        Mensaje = Mensaje & "Detalle:" & vbNewLine
                        Mensaje = Mensaje & Mid(acmd.Parameters(10), 3, Len(acmd.Parameters(10)) - 2)
                        Screen.MousePointer = vbDefault
                    
                        MsgBox Mensaje, vbInformation + vbOKOnly, "Eliminación de Pagos"
                        
                        If lNoPagoNoIden > 0 Then
                         lNoPagoNoIden = lNoPagoNoIden - 1
                        End If
                    Else
                         lNoPagoNoIden = lNoPagoNoIden + 1
                    End If
                    
                    
                    
                End If
            Next
        End If
    End With
    
    cmdCerrar.Enabled = True
    Screen.MousePointer = vbDefault
    MsgBox "Registros Procesados: " & CStr(lNoIdenEliminar + lIdenEliminar) & vbNewLine & "Registros Eliminados: " & CStr(lNoPagoNoIden + lNoPagoIden), vbInformation + vbOKOnly, TITULO_MENSAJE
    Screen.MousePointer = vbHourglass
    pbarElimPagos.Value = 0
    pbarElimPagos.Visible = False
    sbBarraEstado.Panels(1).Text = "Se eliminaron " & CStr(lNoIdenEliminar + lIdenEliminar) & " pagos..."
    'cmdExpExcel.Visible = True
    ctlFiltroConciliacion1.Habilitado = True
    Call InicializarGrids
    cmdEliminacion.Visible = False
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    oAccesoDatos.cnn.DeshacerTrans
    MensajeError Err
End Sub

Private Sub cmdQuitarSel_Click()
    Dim iCont As Integer
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgIdentificados
        .Col = 1
        For iCont = 1 To .Rows - 1
            .Row = iCont
            Set .CellPicture = pbSelNo.Picture
        Next
    End With
    
    With fgNoIdentificados
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
    
    With fgIdentificados
        .Col = 1
        For iCont = 1 To .Rows - 1
            .Row = iCont
            Set .CellPicture = pbSel.Picture
        Next
    End With
    
    With fgNoIdentificados
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

Private Sub ctlFiltroConciliacion1_ClickBuscar()
    Dim oRstConciliar As New clsoAdoRecordset
    Dim sCadenaSQL As String, sMensaje As String

    On Error GoTo RutinaError
    
    If (Trim(ctlFiltroConciliacion1.Codigo) = "") Or (Trim(ctlFiltroConciliacion1.Nombre) = "") Then
        MsgBox "Debe ingresar la Codigo de Grupo y el Ciclo...", vbCritical + vbOKOnly, TITULO_MENSAJE
    Else
        Screen.MousePointer = vbHourglass
        DoEvents
        sbBarraEstado.Panels(1).Text = "Iniciando consulta..."
        dNoRegs = 0
        dMonto = 0
        sstEliminacion.Tab = 0
        Call BorrarFilasGrids
        lbDatoNoRegsTab1.Caption = "0"
        lbMontoTab1.Caption = "$0.00"
        lbNoRegsNoIden.Caption = "0"
        lbMontoNoIden.Caption = "$0.00"
    
        With ctlFiltroConciliacion1
            cmdCerrar.Enabled = False
            'cmdExpExcel.Visible = False
            cmdEliminacion.Visible = False
            cmdSelTodos.Visible = False
            cmdQuitarSel.Visible = False
            ctlFiltroConciliacion1.Habilitado = False
            
            Call RevisaSituacionGPO(.Empresa, .Codigo, .Nombre)
            Call BuscarPagos(.Empresa, .FechaPago, .TipoCliente, .Codigo, .Nombre, .CtaBancaria)
            
            Screen.MousePointer = vbDefault
            sMensaje = ""
            sMensaje = sMensaje & "Se encontraron un total de " & CStr(CDbl(lbDatoNoRegsTab1.Caption) + CDbl(lbNoRegsNoIden.Caption)) & " pagos..." & vbNewLine & vbNewLine
            sMensaje = sMensaje & "     " & lbDatoNoRegsTab1.Caption & vbTab & " Pagos Identificados" & vbNewLine
            sMensaje = sMensaje & "     " & lbNoRegsNoIden.Caption & vbTab & " Pagos No Identificados"
            MsgBox sMensaje, vbInformation + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            
            pbarElimPagos.Value = 0
            pbarElimPagos.Visible = False
            sbBarraEstado.Panels(1).Text = "Se encontraron " & CStr(CDbl(lbDatoNoRegsTab1.Caption) + CDbl(lbNoRegsNoIden.Caption)) & " pagos..."
            
            If (fgIdentificados.Rows - 1 > 0 Or fgNoIdentificados.Rows - 1 > 0) Then
                cmdSelTodos.Visible = True
                cmdQuitarSel.Visible = True
                cmdEliminacion.Visible = True
            End If
            
            cmdCerrar.Enabled = True
            ctlFiltroConciliacion1.Habilitado = True
        End With
    End If
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub BuscarPagos(ByVal psEmpresa As String, ByVal psFechaPago As String, ByVal psTipoCliente As String, ByVal psCodigo As String, ByVal psNombre As String, ByVal psCtaBancaria As String)
    Dim oRstIdentificados As New clsoAdoRecordset, oRstNoIdentificados As New clsoAdoRecordset
    Dim sCadenaSQLIden As String, sCadenaSQLNoIden As String
    Dim sCondEmpresa As String, sCondFechaPago As String, sCondTipoCliente As String
    Dim sCondCodigo As String, sCondNombre1 As String, sCondNombre2 As String, sCondCtaBancaria As String
    
    Dim bValor As Boolean
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    '------------------------------------------------------------------------------------------------------------------------
    '-----                          Procesamos la información para obtener los pagos IDENTIFICADOS                      -----
    '------------------------------------------------------------------------------------------------------------------------
    With ctlFiltroConciliacion1
              
        If (.Empresa = "(Todas)") Then sCondEmpresa = " AND MP.CDGEM = 'EMPFIN' " Else sCondEmpresa = "and      mp.cdgem = '" & .Empresa & "' " & vbNewLine
        If Not (.OptFechaPago) Then sCondFechaPago = "" Else sCondFechaPago = "and      frealdep = '" & Format(.FechaPago, "yyyy/mm/dd") & "' " & vbNewLine
        If (.TipoCliente = "(Todos)") Then sCondTipoCliente = "" Else sCondTipoCliente = "and      clns = '" & Mid(.TipoCliente, 1, 1) & "' " & vbNewLine
        If (.Codigo = "") Then sCondCodigo = "" Else sCondCodigo = "and      cdgclns = '" & .Codigo & "' " & vbNewLine
        If (.Nombre = "") Then
            sCondNombre1 = ""
            sCondNombre2 = ""
        Else
            sCondNombre1 = "and      CICLO = '" & UCase(.Nombre) & "' " & vbNewLine
            sCondNombre2 = "and      CICLO = '" & UCase(.Nombre) & "' " & vbNewLine
        End If
        If (.CtaBancaria = "") Then sCondCtaBancaria = "" Else sCondCtaBancaria = "and      cdgcb = '" & .CtaBancaria & "' " & vbNewLine
    End With
    
    sCadenaSQLIden = ""
    sCadenaSQLIden = sCadenaSQLIden & "select   mp.cdgem, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cdgclns, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.clns, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         decode(mp.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "                         'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cdgcl, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.ciclo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.periodo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.secuencia, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.referencia, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cdgcb, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.tipo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.frealdep, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cantidad, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.modo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.conciliado, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.estatus, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.actualizarpe, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.secuenciaim, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.fechaim, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         nvl(rtrim(ltrim(b.nombre1 || ' ' || b.nombre2)), '') || ' ' || nvl(rtrim(ltrim(b.primape || ' ' || b.segape)), '') nombre " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "from     mp, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         cl b  " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "where    mp.conciliado in ('N', 'C', 'D') " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      mp.estatus    = 'B' " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      b.cdgem       = mp.cdgem " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      b.codigo      = mp.cdgclns " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      mp.clns       = 'I' " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      mp.modo       IN ('I','C') " & vbNewLine
    'sCadenaSQLIden = sCadenaSQLIden & "and      length(mp.referencia) = 9 " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre2 & sCondCtaBancaria
    sCadenaSQLIden = sCadenaSQLIden & "union all " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "select   mp.cdgem, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cdgclns, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.clns, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         decode(mp.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "                         'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cdgcl, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.ciclo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.periodo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.secuencia, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.referencia, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cdgcb, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.tipo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.frealdep, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.cantidad, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.modo, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.conciliado, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.estatus, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.actualizarpe, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.secuenciaim, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         mp.fechaim, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         rtrim(ltrim(nombre)) nombre " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "from     mp, " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "         ns b " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "where    mp.conciliado in ('N', 'C', 'D') " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      mp.estatus    = 'B' " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      b.cdgem       = mp.cdgem " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      b.codigo      = mp.cdgclns " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      mp.clns       = 'G' " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "and      mp.modo       IN ('I','C') " & vbNewLine
    'sCadenaSQLIden = sCadenaSQLIden & "and      length(mp.referencia) = 9 " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre1 & sCondCtaBancaria
    sCadenaSQLIden = sCadenaSQLIden & "order by frealdep, referencia, secuencia"
    
    If (oRstIdentificados.Estado = adStateOpen) Then oRstIdentificados.Cerrar
    
    oRstIdentificados.Abrir sCadenaSQLIden, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstIdentificados.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
'            Screen.MousePointer = vbDefault
'            MsgBox "No se encontraron pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
'            oRstIdentificados.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con los pagos identificados   -----
            pbarElimPagos.Value = 0
            pbarElimPagos.Max = oRstIdentificados.NumeroRegistros
            pbarElimPagos.Visible = True
            
            While Not oRstIdentificados.FinDeArchivo
                pbarElimPagos.Value = Val(lbDatoNoRegsTab1.Caption)
                sbBarraEstado.Panels(1).Text = "Obteniendo pago identificado no. " & CStr(lbDatoNoRegsTab1.Caption + 1) & " de " & CStr(oRstIdentificados.NumeroRegistros) & "  (" & CStr(Format(((lbDatoNoRegsTab1.Caption + 1) * 100) / oRstIdentificados.NumeroRegistros, "##0.00")) & "%)"
                Call PonerPagosIdentificados(oRstIdentificados)
                oRstIdentificados.IrAlRegSiguiente
            Wend
'''
'''            Screen.MousePointer = vbDefault
'''            MsgBox "Se encontraron un total de " & lbDatoNoRegsTab1.Caption & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
'''            Screen.MousePointer = vbHourglass
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la lista de pagos..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstIdentificados.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstIdentificados.Estado = adStateOpen) Then oRstIdentificados.Cerrar
    
    '------------------------------------------------------------------------------------------------------------------------
    '-----                        Procesamos la información para obtener los pagos de GARANTIA LIQUIDA                     -----
    '------------------------------------------------------------------------------------------------------------------------
    With ctlFiltroConciliacion1
        If (.Empresa = "(Todas)") Then sCondEmpresa = "" Else sCondEmpresa = "and       pdi.cdgem = '" & .Empresa & "' " & vbNewLine
        If Not (.OptFechaPago) Then sCondFechaPago = "" Else sCondFechaPago = "and       pdi.fdeposito = '" & Format(.FechaPago, "yyyy/mm/dd") & "' " & vbNewLine
        If (.TipoCliente = "(Todos)") Then sCondTipoCliente = "" Else sCondTipoCliente = "and       pdi.clns = '" & Mid(.TipoCliente, 1, 1) & "' " & vbNewLine
        If (.Codigo = "") Then sCondCodigo = "" Else sCondCodigo = "and       pdi.cdgclns = '" & .Codigo & "' " & vbNewLine
        If (.Nombre = "") Then
            sCondNombre1 = ""
            sCondNombre2 = ""
        Else
            sCondNombre1 = "and       CICLO = '" & UCase(.Nombre) & "' " & vbNewLine
            sCondNombre2 = "and       CICLO = '" & UCase(.Nombre) & "' " & vbNewLine
        End If
        If (.CtaBancaria = "") Then sCondCtaBancaria = "" Else sCondCtaBancaria = "and       pdi.cdgcb = '" & .CtaBancaria & "' " & vbNewLine
    End With
    
    sCadenaSQLNoIden = ""
    sCadenaSQLNoIden = sCadenaSQLNoIden & "select    pdi.cdgem, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.cdgclns, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.clns, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          decode(pdi.clns, 'G' , 'Grupal', " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "                           'I',  'Individual') tipocte, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.ciclo, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.secpago secuencia, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.referencia, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.cdgcb, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.fpago fdeposito, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.cantidad, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.secpago secuenciaim, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          pdi.fpago fechaim, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          'Pago de Garantía Líquida.' descripcion, " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "          rtrim(ltrim(b.nombre)) nombre " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "from      PAG_GAR_SIM pdi " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "left join ns b " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "on        b.cdgem                = pdi.cdgem " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "and       b.codigo               = pdi.cdgclns " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "where     pdi.clns                = 'G' " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & "and       pdi.estatus = 'RE' " & vbNewLine
    sCadenaSQLNoIden = sCadenaSQLNoIden & sCondEmpresa & sCondFechaPago & sCondTipoCliente & sCondCodigo & sCondNombre1 & sCondCtaBancaria
    sCadenaSQLNoIden = sCadenaSQLNoIden & "order by  fdeposito, referencia, secuencia"
    
    If (oRstNoIdentificados.Estado = adStateOpen) Then oRstNoIdentificados.Cerrar
    
    oRstNoIdentificados.Abrir sCadenaSQLNoIden, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstNoIdentificados.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
'            Screen.MousePointer = vbDefault
'            MsgBox "No se encontraron pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
'            oRstNoIdentificados.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con los pagos no identificados   -----
            pbarElimPagos.Value = 0
            pbarElimPagos.Max = oRstNoIdentificados.NumeroRegistros
            pbarElimPagos.Visible = True
            
            While Not oRstNoIdentificados.FinDeArchivo
                pbarElimPagos.Value = (lbNoRegsNoIden.Caption)
                sbBarraEstado.Panels(1).Text = "Obteniendo pago no identificado no. " & CStr(lbNoRegsNoIden.Caption + 1) & " de " & CStr(oRstNoIdentificados.NumeroRegistros) & "  (" & CStr(Format(((lbNoRegsNoIden.Caption + 1) * 100) / oRstNoIdentificados.NumeroRegistros, "##0.00")) & "%)"
                Call PonerPagosNoIdentificados(oRstNoIdentificados)
                oRstNoIdentificados.IrAlRegSiguiente
            Wend
'''
'''            Screen.MousePointer = vbDefault
'''            MsgBox "Se encontraron un total de " & lbDatoNoRegsTab1.Caption & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
'''            Screen.MousePointer = vbHourglass
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la lista de pagos..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstNoIdentificados.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstNoIdentificados.Estado = adStateOpen) Then oRstNoIdentificados.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerPagosIdentificados(ByVal poRst As clsoAdoRecordset)
    Dim sFechaCarga As String, vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    sFechaCarga = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
    With Me.fgIdentificados
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
        Set .CellPicture = pbSelNo.Picture
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
        .CellAlignment = flexAlignCenterCenter
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
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuencia").Value), "", poRst.ObjSetRegistros.Fields("secuencia").Value)
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

Private Sub PonerPagosNoIdentificados(ByVal poRst As clsoAdoRecordset)
    Dim sFechaCarga As String, vColorFrente As Variant, vColorFondo As Variant
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    sFechaCarga = Format(Date, "dd/mm/yyyy") & " " & Format(Time(), "hh:nn:ss am/pm")
    With fgNoIdentificados
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
        Set .CellPicture = pbSelNo.Picture
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
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fdeposito").Value), "", poRst.ObjSetRegistros.Fields("fdeposito").Value)
        .Col = 5
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
        .Col = 6
        .CellAlignment = flexAlignLeftCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("nombre").Value), "", poRst.ObjSetRegistros.Fields("nombre").Value), "$###,###,###,##0.00")
        .Col = 7
        .CellAlignment = flexAlignRightCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,##0.00")
        .Col = 8
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
        .Col = 9
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuencia").Value), "", poRst.ObjSetRegistros.Fields("secuencia").Value)
        .Col = 10
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuenciaim").Value), "", poRst.ObjSetRegistros.Fields("secuenciaim").Value)
        .Col = 11
        .CellAlignment = flexAlignCenterCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fechaim").Value), "", poRst.ObjSetRegistros.Fields("fechaim").Value)
        .Col = 12
        .CellAlignment = flexAlignLeftCenter
        .CellForeColor = vColorFrente
        .CellBackColor = vColorFondo
        .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("descripcion").Value), "", poRst.ObjSetRegistros.Fields("descripcion").Value)
    End With
    
    lbNoRegsNoIden.Caption = CStr(CDbl(lbNoRegsNoIden.Caption) + 1)
    DoEvents
    lbMontoNoIden.Caption = Format(CStr(CDbl(lbMontoNoIden.Caption) + poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,##0.00")
    DoEvents
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub


Private Sub BorrarFilasGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    fgIdentificados.Rows = 1
    fgIdentificados.Refresh
    
    fgNoIdentificados.Rows = 1
    fgNoIdentificados.Refresh
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub InicializarGrids()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    '-----   Inicializamos el Grid para los pagos por eliminar   -----
    With fgIdentificados
        .Rows = 1
        .Cols = 15
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 600
        
        .Col = 1
        .CellPictureAlignment = flexAlignLeftCenter
        Set .CellPicture = pbCesto.Picture
        .TextMatrix(0, 1) = "Eliminar"
        .ColAlignment(1) = flexAlignRightCenter
        .ColWidth(1) = 900
        
        .Col = 2
        .TextMatrix(0, 2) = "Fecha de Eliminación"
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
        .TextMatrix(0, 10) = "Secuencia IM"
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
        .TextMatrix(0, 14) = "Secuencia MP"
        .ColAlignment(14) = flexAlignCenterCenter
        .ColWidth(14) = 1200
    End With
    '-----   Inicializamos el Grid para los pagos eliminados   -----
    With fgNoIdentificados
        .Rows = 1
        .Cols = 13
        
        .Col = 0
        .TextMatrix(0, 0) = "No."
        .ColAlignment(0) = flexAlignCenterCenter
        .ColWidth(0) = 600
        
        .Col = 1
        .CellPictureAlignment = flexAlignLeftCenter
        Set .CellPicture = pbCesto.Picture
        .TextMatrix(0, 1) = "Eliminar"
        .ColAlignment(1) = flexAlignRightCenter
        .ColWidth(1) = 900
        
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
        .TextMatrix(0, 6) = "Nombre"
        .ColAlignment(6) = flexAlignCenterCenter
        .ColWidth(6) = 3000
        
        .Col = 7
        .TextMatrix(0, 7) = "Monto"
        .ColAlignment(7) = flexAlignCenterCenter
        .ColWidth(7) = 1100
        
        .Col = 8
        .TextMatrix(0, 8) = "Cta. Bancaria"
        .ColAlignment(8) = flexAlignCenterCenter
        .ColWidth(8) = 1200
        
        .Col = 9
        .TextMatrix(0, 9) = "SecuenciaPDI"
        .ColAlignment(9) = flexAlignCenterCenter
        .ColWidth(9) = 1200
        
        .Col = 10
        .TextMatrix(0, 10) = "SecuenciaIM"
        .ColAlignment(10) = flexAlignCenterCenter
        .ColWidth(10) = 1200
        
        .Col = 11
        .TextMatrix(0, 11) = "FechaIM"
        .ColAlignment(11) = flexAlignCenterCenter
        .ColWidth(11) = 1200
        
        .Col = 12
        .TextMatrix(0, 12) = "Descripción"
        .ColAlignment(12) = flexAlignCenterCenter
        .ColWidth(12) = 5000
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
    ctlFiltroConciliacion1.OptFechaPago = False
End Sub

Private Sub ctlFiltroConciliacion1_ClickNombre()
    If (sbBarraEstado.Panels(1).Text <> TITULO_MOD_DEL) Then sbBarraEstado.Panels(1).Text = TITULO_MOD_DEL
    ctlFiltroConciliacion1.OptNombre = True
    ctlFiltroConciliacion1.OptCodigo = True
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

Private Sub fgIdentificados_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    With fgIdentificados
        If (.Col = 1) Then
            If (.CellPicture = pbSel.Picture) Then
                Set .CellPicture = pbSelNo.Picture
            Else
                Set .CellPicture = pbSel.Picture
            End If
        End If
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub fgNoIdentificados_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    
    With fgNoIdentificados
        If (.Col = 1) Then
            If (.CellPicture = pbSel.Picture) Then
                Set .CellPicture = pbSelNo.Picture
            Else
                Set .CellPicture = pbSel.Picture
            End If
        End If
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    sstEliminacion.Tab = 0
    Call InicializarGrids
    cmdEliminacion.Visible = False
        
    '-----   La eliminación de pagos solo podrá ser por archivo importado   -----
    With ctlFiltroConciliacion1
        .OptCodigo = True
        .OptCtaBancaria = False
        .OptEmpresa = False
        .OptFechaPago = False
        .OptNombre = True
        .OptTipoCliente = False
    End With

    ctlFiltroConciliacion1.QuitarFiltro = False

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    pbarElimPagos.Width = sbBarraEstado.Panels(2).Width - 40
    pbarElimPagos.Top = sbBarraEstado.Top + 60
    pbarElimPagos.Left = sbBarraEstado.Panels(1).Width + 80
    pbarElimPagos.Height = sbBarraEstado.Height - 100
    
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

Private Sub RevisaSituacionGPO(ByVal psEmpresa As String, ByVal psCodigo As String, ByVal psNombre As String)
    Dim oRstIdentificados As New clsoAdoRecordset
    Dim sCadenaSQLIden As String
    Dim sCondEmpresa As String
    Dim sCondCodigo As String, sCondNombre1 As String
    
    Dim bValor As Boolean
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    '------------------------------------------------------------------------------------------------------------------------
    '-----                          Validamos la situación del crédito                      -----
    '------------------------------------------------------------------------------------------------------------------------
    With ctlFiltroConciliacion1
              
        If (.Empresa = "(Todas)") Then sCondEmpresa = " b.CDGEM = 'EMPFIN' " Else sCondEmpresa = "    b.cdgem = '" & .Empresa & "' " & vbNewLine
        sCondCodigo = "and      b.cdgns = '" & .Codigo & "' " & vbNewLine
        sCondNombre1 = "and      b.CICLO = '" & UCase(.Nombre) & "' " & vbNewLine
    End With
    
    sCadenaSQLIden = ""
    sCadenaSQLIden = sCadenaSQLIden & "select   b.situacion " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "from     prn b " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & "where    " & vbNewLine
    sCadenaSQLIden = sCadenaSQLIden & sCondEmpresa & sCondCodigo & sCondNombre1
    
    If (oRstIdentificados.Estado = adStateOpen) Then oRstIdentificados.Cerrar
    
    oRstIdentificados.Abrir sCadenaSQLIden, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly
    
    Select Case oRstIdentificados.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
'            Screen.MousePointer = vbDefault
'            MsgBox "No se encontraron pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
'            oRstIdentificados.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con los pagos identificados   -----
            While Not oRstIdentificados.FinDeArchivo
                SitGpo = oRstIdentificados.ObjSetRegistros.Fields("situacion").Value
                oRstIdentificados.IrAlRegSiguiente
            Wend
            
            If SitGpo = "L" Then
                MsgBox "La situación actual del crédito es: LIQUIDADO." & vbNewLine & "En caso de que algún pago sea eliminado el crédito se reactiviara.", vbInformation + vbOKOnly, TITULO_MENSAJE
            End If
'''
'''            Screen.MousePointer = vbDefault
'''            MsgBox "Se encontraron un total de " & lbDatoNoRegsTab1.Caption & " pagos...", vbInformation + vbOKOnly, TITULO_MENSAJE
'''            Screen.MousePointer = vbHourglass
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo obtener la situación del grupo..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstIdentificados.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstIdentificados.Estado = adStateOpen) Then oRstIdentificados.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

