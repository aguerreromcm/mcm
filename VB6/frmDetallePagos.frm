VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Begin VB.Form frmDetallePagos 
   AutoRedraw      =   -1  'True
   BackColor       =   &H00FFFFFF&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Detalle de los pagos"
   ClientHeight    =   7125
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   11445
   Icon            =   "frmDetallePagos.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   7125
   ScaleWidth      =   11445
   StartUpPosition =   2  'CenterScreen
   Begin AdminCred.ctlBoton cmdCerrar 
      Height          =   270
      Left            =   10140
      TabIndex        =   1
      Top             =   6480
      Width           =   1260
      _ExtentX        =   2223
      _ExtentY        =   476
   End
   Begin MSFlexGridLib.MSFlexGrid fgPagos 
      Height          =   5760
      Left            =   30
      TabIndex        =   0
      Top             =   630
      Width           =   11385
      _ExtentX        =   20082
      _ExtentY        =   10160
      _Version        =   393216
      BackColorFixed  =   14737632
      BackColorBkg    =   -2147483632
      SelectionMode   =   1
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
   Begin MSComctlLib.ProgressBar pbarConsultar 
      Height          =   195
      Left            =   5070
      TabIndex        =   2
      Top             =   6900
      Width           =   1995
      _ExtentX        =   3519
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   3
      Top             =   6840
      Width           =   11445
      _ExtentX        =   20188
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de consulta de pagos "
            TextSave        =   "Módulo de consulta de pagos "
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
   Begin VB.Label lbTipoPago 
      Alignment       =   2  'Center
      BackColor       =   &H00FFD0D0&
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Tipo de Pago"
      BeginProperty Font 
         Name            =   "Verdana"
         Size            =   9.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   615
      Left            =   30
      TabIndex        =   4
      Top             =   0
      Width           =   11385
   End
   Begin VB.Menu mnuOpciones 
      Caption         =   "&Opciones"
      Visible         =   0   'False
      Begin VB.Menu mnuImportar 
         Caption         =   "&Importar"
      End
      Begin VB.Menu mnuSep1 
         Caption         =   "-"
      End
      Begin VB.Menu mnuEdoCta 
         Caption         =   "Estado de &cuenta"
         Begin VB.Menu mnuGenReporte 
            Caption         =   "&Generar reporte"
         End
         Begin VB.Menu mnuCorregirEdoCta 
            Caption         =   "&Corregir"
         End
      End
      Begin VB.Menu mnuSep2 
         Caption         =   "-"
      End
      Begin VB.Menu mnuEliminar 
         Caption         =   "&Eliminar"
      End
      Begin VB.Menu mnuModificar 
         Caption         =   "&Modificar"
      End
   End
End
Attribute VB_Name = "frmDetallePagos"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private bCerrarForm As Boolean
Private lNoRegs As Long

Private Sub cmdCerrar_BotonClick()
    bCerrarForm = True
    Unload Me
End Sub

Private Sub fgPagos_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    On Error GoTo RutinaError

    If Button = 2 And fgPagos.Rows > 1 Then
        PopupMenu mnuOpciones
    End If

    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    bCerrarForm = False
    cmdCerrar.Texto = "&Cerrar"
    Call LlenarGrid
    
    Select Case cCtaBancaria.TipoPagos
        Case "1"    'Identificados
            mnuImportar.Enabled = False
            mnuEdoCta.Enabled = True
            mnuEliminar.Enabled = False
            mnuModificar.Enabled = False
        Case "2"    'No Identificados
            mnuImportar.Enabled = True
            mnuEdoCta.Enabled = False
            mnuEliminar.Enabled = True
            mnuModificar.Enabled = True
        Case "3"    'No Importados
            mnuImportar.Enabled = True
            mnuEdoCta.Enabled = False
            mnuEliminar.Enabled = True
            mnuModificar.Enabled = True
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    pbarConsultar.Width = sbBarraEstado.Panels(2).Width - 40
    pbarConsultar.Top = sbBarraEstado.Top + 60
    pbarConsultar.Left = sbBarraEstado.Panels(1).Width + 80
    pbarConsultar.Height = sbBarraEstado.Height - 100
    
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

Private Sub LlenarGrid()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Select Case cCtaBancaria.TipoPagos
        Case "1"
            Call PonerIdentificados
        Case "2"
            Call PonerNoIdentificados
        Case "3"
            Call PonerNoImportados
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerIdentificados()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call InicializaGrid(cCtaBancaria.TipoPagos)
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select   * " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from     mp " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where    frealdep = '" & Format(cCtaBancaria.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgcb    is not null " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      estatus  <> 'E' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgem    = '" & cCtaBancaria.Empresa & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and      cdgcb    = '" & cCtaBancaria.NoCta & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by frealdep, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "         cdgclns "
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
'''''            Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 3)
'''''            Call InsertarSeparador(fgPagosFINSOL, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
'''''            lNoPagos = 0
'''''            lCantidad = 0
'''''            lCapital = 0
'''''            lIntereses = 0
'''''            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call PonerPagos(oRstConsultar)
                oRstConsultar.IrAlRegSiguiente
            Wend

'''''            Call InsertarTot(fgPagosFINSOL, 1, &HFF8080)
'''''
'''''            bTotNoPagos = bTotNoPagos + lNoPagos
'''''            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerNoIdentificados()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call InicializaGrid(cCtaBancaria.TipoPagos)
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select        * " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from          PAG_GAR_SIM " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where         fpago = '" & Format(cCtaBancaria.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgem = '" & cCtaBancaria.Empresa & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgcb = '" & cCtaBancaria.NoCta & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           estatus = 'RE' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              referencia "
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
'''''            Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 3)
'''''            Call InsertarSeparador(fgPagosFINSOL, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
'''''            lNoPagos = 0
'''''            lCantidad = 0
'''''            lCapital = 0
'''''            lIntereses = 0
'''''            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call PonerPagos(oRstConsultar)
                oRstConsultar.IrAlRegSiguiente
            Wend

'''''            Call InsertarTot(fgPagosFINSOL, 1, &HFF8080)
'''''
'''''            bTotNoPagos = bTotNoPagos + lNoPagos
'''''            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerNoImportados()
    Dim sCadenaSQL As String, oRstConsultar As New clsoAdoRecordset
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    Call InicializaGrid(cCtaBancaria.TipoPagos)
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
'    sCadenaSQL = ""
'    sCadenaSQL = sCadenaSQL & "select   * " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "from     res_impor " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "where    fechapago = '" & cCtaBancaria.FechaPago & "' " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "and      validacion in (2, 4) " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "and      cdgem = '" & cCtaBancaria.Empresa & "' " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "and      ctabancaria = '" & cCtaBancaria.NoCta & "' " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "order by cdgem, " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "         ctabancaria, " & vbNewLine
'    sCadenaSQL = sCadenaSQL & "         referencia "
    
    sCadenaSQL = ""
    sCadenaSQL = sCadenaSQL & "select        * " & vbNewLine
    sCadenaSQL = sCadenaSQL & "from          pdi " & vbNewLine
    sCadenaSQL = sCadenaSQL & "where         fdeposito = '" & Format(cCtaBancaria.FechaPago, "YYYY/MM/DD") & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgem = '" & cCtaBancaria.Empresa & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           cdgcb = '" & cCtaBancaria.NoCta & "' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "and           estatus = 'RE' " & vbNewLine
    sCadenaSQL = sCadenaSQL & "order by      cdgem, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              cdgcb, " & vbNewLine
    sCadenaSQL = sCadenaSQL & "              referencia "
    
    oRstConsultar.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstConsultar.HayRegistros
        Case 0  '-----   La consulta no retorno registros.   -----
'''''            Call LlenarGrid(fgPagosFINSOL, oRstConsultar, 3)
'''''            Call InsertarSeparador(fgPagosFINSOL, vbBlack)
            Screen.MousePointer = vbDefault
            oRstConsultar.Cerrar
        Case 1  '-----   Hay registros.                       -----
            '-----   Llenamos el grid con la información encontrada   -----
            pbarConsultar.Value = 0
            pbarConsultar.Max = oRstConsultar.NumeroRegistros
            pbarConsultar.Visible = True
            lNoRegs = 0
'''''            lNoPagos = 0
'''''            lCantidad = 0
'''''            lCapital = 0
'''''            lIntereses = 0
'''''            lRecargos = 0

            While Not oRstConsultar.FinDeArchivo
                lNoRegs = lNoRegs + 1
                pbarConsultar.Value = lNoRegs
                sbBarraEstado.Panels(1).Text = "Obteniendo registro no. " & CStr(lNoRegs) & " de " & CStr(oRstConsultar.NumeroRegistros) & "  (" & CStr(Format((lNoRegs * 100) / oRstConsultar.NumeroRegistros, "##0.00")) & "%)"
                Call PonerPagos(oRstConsultar)
                oRstConsultar.IrAlRegSiguiente
            Wend

'''''            Call InsertarTot(fgPagosFINSOL, 1, &HFF8080)
'''''
'''''            bTotNoPagos = bTotNoPagos + lNoPagos
'''''            bTotCantidad = bTotCantidad + lCantidad

            sbBarraEstado.Panels(1).Text = "Se obtuvieron " & CStr(lNoRegs) & " registros..."
            pbarConsultar.Value = 0
        Case 2  '-----   El Query no se pudo ejecutar.        -----
            Screen.MousePointer = vbDefault
            MsgBox "La aplicación no pudo realizar la consulta..." & vbNewLine & "Intente nuevamente o consulte al administrador del sistema.", vbCritical + vbOKOnly, TITULO_MENSAJE
            Screen.MousePointer = vbHourglass
            oRstConsultar.Cerrar
            Screen.MousePointer = vbDefault
    End Select
    
    If (oRstConsultar.Estado = adStateOpen) Then oRstConsultar.Cerrar
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub InicializaGrid(psTipoPagos As String)
    Dim vColorFrente As Variant, vColorFondo As Variant

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    With fgPagos
        .Rows = 1
        .Row = .Rows - 1
        
        If (.Row Mod 2 = 1) Then
            vColorFrente = vbBlack
            vColorFondo = &HFFF5F5
            'vColorFondo = vbWhite
        Else
            vColorFrente = vbBlack
            vColorFondo = vbWhite
        End If
        
        Select Case cCtaBancaria.TipoPagos
            Case "1"
                .Cols = 14
                
                .Col = 0
                .ColWidth(0) = 500
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "No."
                
                .Col = 1
                .ColWidth(1) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Empresa"
                
                .Col = 2
                .ColWidth(2) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Fecha pago"
                
                .Col = 3
                .ColWidth(3) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Código"
                
                .Col = 4
                .ColWidth(4) = 800
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Tipo Cte."
                
                .Col = 5
                .ColWidth(5) = 800
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Ciclo"
                
                .Col = 6
                .ColWidth(6) = 1200
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cta. Bancaria"
                
                .Col = 7
                .ColWidth(7) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cantidad"
                
                .Col = 8
                .ColWidth(8) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Capital"
                
                .Col = 9
                .ColWidth(9) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Intereses"
                
                .Col = 10
                .ColWidth(10) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Recargos"
                
                .Col = 11
                .ColWidth(11) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Conciliado"
                
                .Col = 12
                .ColWidth(12) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "SecuenciaMP"
                
                .Col = 13
                .ColWidth(13) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "SecuenciaIM"
            Case "2"
                .Cols = 7
                
                .Col = 0
                .ColWidth(0) = 500
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "No."
                
                .Col = 1
                .ColWidth(1) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Empresa"
                
                .Col = 2
                .ColWidth(2) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Fecha pago"
                
                .Col = 3
                .ColWidth(3) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Referencia"
                
                .Col = 4
                .ColWidth(4) = 1200
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cta. Bancaria"
                
                .Col = 5
                .ColWidth(5) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cantidad"
                
                .Col = 6
                .ColWidth(6) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "SecuenciaIM"
            Case "3"
                .Cols = 9
                
                .Col = 0
                .ColWidth(0) = 500
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "No."
                
                .Col = 1
                .ColWidth(1) = 1000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Empresa"
                
                .Col = 2
                .ColWidth(2) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Fecha pago"
                
                .Col = 3
                .ColWidth(3) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Referencia"
                
                .Col = 4
                .ColWidth(4) = 1200
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cta. Bancaria"
                
                .Col = 5
                .ColWidth(5) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Cantidad"
                
                .Col = 6
                .ColWidth(6) = 9000
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Observaciones"
                
                .Col = 7
                .ColWidth(7) = 1300
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "SecuenciaIM"
                
                .Col = 8
                .ColWidth(8) = 2100
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .CellFontBold = True
                .Text = "Fecha carga"
        End Select
    End With
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub PonerPagos(ByVal poRst As clsoAdoRecordset)
    Dim vColorFrente As Variant, vColorFondo As Variant, sFechaCarga As String

    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    If (fgPagos.Row Mod 2 = 1) Then
        vColorFrente = vbBlack
        vColorFondo = &HFFF5F5
        'vColorFondo = vbWhite
    Else
        vColorFrente = vbBlack
        vColorFondo = vbWhite
    End If

    With fgPagos
        .Rows = .Rows + 1
        .Row = .Rows - 1
        Select Case cCtaBancaria.TipoPagos
            Case "1"
                lbTipoPago.Caption = "PAGOS IDENTIFICADOS" & vbNewLine & " (Empresa: " & cCtaBancaria.Empresa & ", Cta. Bancaria: " & cCtaBancaria.NoCta & ", Fecha pago: " & cCtaBancaria.FechaPago & ")"
            
                .Col = 0
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .Text = CStr(.Row) & " "
                
                .Col = 1
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
                
                .Col = 2
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("frealdep").Value), "", poRst.ObjSetRegistros.Fields("frealdep").Value)
                
                .Col = 3
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgclns").Value), "", poRst.ObjSetRegistros.Fields("cdgclns").Value)
                
                .Col = 4
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("clns").Value), "", poRst.ObjSetRegistros.Fields("clns").Value)
                
                .Col = 5
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("ciclo").Value), "", poRst.ObjSetRegistros.Fields("ciclo").Value)
                
                .Col = 6
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
                
                .Col = 7
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 8
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("pagadocap").Value), "$0.00", poRst.ObjSetRegistros.Fields("pagadocap").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 9
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("pagadoint").Value), "$0.00", poRst.ObjSetRegistros.Fields("pagadoint").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 10
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("pagadorec").Value), "$0.00", poRst.ObjSetRegistros.Fields("pagadorec").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 11
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("conciliado").Value), "", poRst.ObjSetRegistros.Fields("conciliado").Value)
                
                .Col = 12
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuencia").Value), "", poRst.ObjSetRegistros.Fields("secuencia").Value)
                
                .Col = 13
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuenciaim").Value), "", poRst.ObjSetRegistros.Fields("secuenciaim").Value)
            Case "2"
                lbTipoPago.Caption = "GARANTIAS" & vbNewLine & " (Empresa: " & cCtaBancaria.Empresa & ", Cta. Bancaria: " & cCtaBancaria.NoCta & ", Fecha pago: " & cCtaBancaria.FechaPago & ")"
            
                .Col = 0
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .Text = CStr(.Row) & " "
                
                .Col = 1
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
                
                .Col = 2
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fpago").Value), "", poRst.ObjSetRegistros.Fields("fpago").Value)
                
                .Col = 3
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
                
                .Col = 4
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
                
                .Col = 5
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 6
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = ""
            Case "3"
                lbTipoPago.Caption = "PAGOS NO IMPORTADOS" & vbNewLine & " (Empresa: " & cCtaBancaria.Empresa & ", Cta. Bancaria: " & cCtaBancaria.NoCta & ", Fecha pago: " & cCtaBancaria.FechaPago & ")"
            
                .Col = 0
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vbBlack
                .CellBackColor = &HE0E0E0
                .Text = CStr(.Row) & " "
                
                .Col = 1
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgem").Value), "", poRst.ObjSetRegistros.Fields("cdgem").Value)
                
                .Col = 2
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fdeposito").Value), "", poRst.ObjSetRegistros.Fields("fdeposito").Value)
                
                .Col = 3
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("referencia").Value), "", poRst.ObjSetRegistros.Fields("referencia").Value)
                
                .Col = 4
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("cdgcb").Value), "", poRst.ObjSetRegistros.Fields("cdgcb").Value)
                
                .Col = 5
                .CellAlignment = flexAlignRightCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = Format(IIf(IsNull(poRst.ObjSetRegistros.Fields("cantidad").Value), "$0.00", poRst.ObjSetRegistros.Fields("cantidad").Value), "$###,###,###,###,###,##0.00")
                
                .Col = 6
                .CellAlignment = flexAlignLeftCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("descripcion").Value), "", Replace(Replace(Replace(Replace(poRst.ObjSetRegistros.Fields("descripcion").Value, Chr(13), ""), Chr(10), ""), vbTab, " "), "Resultado de la validación:", ""))

                .Col = 7
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("secuenciaim").Value), "", poRst.ObjSetRegistros.Fields("secuenciaim").Value)
                
                .Col = 8
                .CellAlignment = flexAlignCenterCenter
                .CellForeColor = vColorFrente
                .CellBackColor = vColorFondo
                .Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("fechaim").Value), "", poRst.ObjSetRegistros.Fields("fechaim").Value)
'                If (Not IsNull(poRst.ObjSetRegistros.Fields("identificador").Value)) Then
'                    sFechaCarga = Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 1, 2) & "/" & Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 3, 2) & "/" & Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 5, 4) & " " & Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 9, 2) & ":" & Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 11, 2) & ":" & Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 13, 2)
'                    If (Val(Mid(poRst.ObjSetRegistros.Fields("identificador").Value, 9, 2)) >= 12) Then
'                        sFechaCarga = sFechaCarga & " pm"
'                    Else
'                        sFechaCarga = sFechaCarga & " am"
'                    End If
'                    .Text = sFechaCarga
'                Else
'                    .Text = ""
'                End If
                
                '.Text = IIf(IsNull(poRst.ObjSetRegistros.Fields("identificador").Value), "", poRst.ObjSetRegistros.Fields("identificador").Value)
        End Select
    End With

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub mnuCorregirEdoCta_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Screen.MousePointer = vbDefault
    MsgBox "En construcción...", vbInformation + vbOKOnly, "Mensaje"

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

'Private Sub mnuEdoCta_Click()
'    On Error GoTo RutinaError
'    Screen.MousePointer = vbHourglass
'
'    Screen.MousePointer = vbDefault
'    MsgBox "En construcción...", vbInformation + vbOKOnly, "Mensaje"
'
'    Screen.MousePointer = vbDefault
'    Exit Sub
'RutinaError:
'    MensajeError Err
'End Sub

Private Sub mnuEliminar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Screen.MousePointer = vbDefault
    MsgBox "En construcción...", vbInformation + vbOKOnly, "Mensaje"

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub mnuGenReporte_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    frmVisualizador.Show 1, Me

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub mnuImportar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Screen.MousePointer = vbDefault
    MsgBox "En construcción...", vbInformation + vbOKOnly, "Mensaje"

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub mnuModificar_Click()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    Screen.MousePointer = vbDefault
    MsgBox "En construcción...", vbInformation + vbOKOnly, "Mensaje"

    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub
