VERSION 5.00
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmPolizas 
   AutoRedraw      =   -1  'True
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Póliza"
   ClientHeight    =   3735
   ClientLeft      =   2340
   ClientTop       =   2115
   ClientWidth     =   9915
   BeginProperty Font 
      Name            =   "Verdana"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3735
   ScaleWidth      =   9915
   Begin VB.PictureBox pbEncabezado 
      Align           =   1  'Align Top
      BackColor       =   &H00800000&
      BorderStyle     =   0  'None
      Height          =   735
      Left            =   0
      ScaleHeight     =   752.5
      ScaleMode       =   0  'User
      ScaleWidth      =   9915
      TabIndex        =   6
      Top             =   0
      Width           =   9915
      Begin VB.PictureBox Picture2 
         Height          =   735
         Left            =   360
         Picture         =   "frmPolizas.frx":0000
         ScaleHeight     =   675
         ScaleWidth      =   1035
         TabIndex        =   14
         Top             =   0
         Width           =   1095
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         BackStyle       =   0  'Transparent
         Caption         =   "Póliza"
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
         Left            =   2040
         TabIndex        =   9
         Top             =   60
         Width           =   840
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
         TabIndex        =   8
         Top             =   180
         Width           =   1170
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
         TabIndex        =   7
         Top             =   90
         Width           =   135
      End
   End
   Begin MSComctlLib.ProgressBar pbarConciliacion 
      Height          =   195
      Left            =   5070
      TabIndex        =   0
      Top             =   8250
      Width           =   1995
      _ExtentX        =   3519
      _ExtentY        =   344
      _Version        =   393216
      Appearance      =   0
   End
   Begin VB.PictureBox pbContenido 
      Align           =   1  'Align Top
      BackColor       =   &H00FFFFFF&
      Height          =   2640
      Left            =   0
      ScaleHeight     =   2580
      ScaleWidth      =   9855
      TabIndex        =   2
      Top             =   735
      Width           =   9915
      Begin VB.Frame Frame1 
         Caption         =   "Póliza"
         BeginProperty Font 
            Name            =   "Verdana"
            Size            =   6.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   2040
         Left            =   30
         TabIndex        =   10
         Top             =   20
         Width           =   9840
         Begin MSComDlg.CommonDialog cmmPolizas 
            Left            =   9240
            Top             =   1320
            _ExtentX        =   847
            _ExtentY        =   847
            _Version        =   393216
         End
         Begin MSComCtl2.DTPicker DTPCalendario 
            Height          =   615
            Left            =   6360
            TabIndex        =   13
            Top             =   480
            Width           =   2535
            _ExtentX        =   4471
            _ExtentY        =   1085
            _Version        =   393216
            BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
               Name            =   "Verdana"
               Size            =   18
               Charset         =   0
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            Format          =   50790401
            CurrentDate     =   40576
         End
         Begin VB.Label lblMensaje2 
            Alignment       =   2  'Center
            Caption         =   "Procesando.... "
            BeginProperty Font 
               Name            =   "Verdana"
               Size            =   15.75
               Charset         =   0
               Weight          =   700
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000D&
            Height          =   375
            Left            =   480
            TabIndex        =   12
            Top             =   1440
            Width           =   8655
         End
         Begin VB.Label lblMensaje 
            Alignment       =   2  'Center
            Caption         =   "Póliza"
            BeginProperty Font 
               Name            =   "Verdana"
               Size            =   20.25
               Charset         =   0
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            Height          =   1095
            Left            =   120
            TabIndex        =   11
            Top             =   240
            Width           =   6015
         End
      End
      Begin VB.PictureBox Picture1 
         Height          =   30
         Left            =   3090
         ScaleHeight     =   30
         ScaleWidth      =   30
         TabIndex        =   5
         Top             =   270
         Width           =   30
      End
      Begin VB.CommandButton cmdCierre 
         Caption         =   "Generar Archivo"
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
         TabIndex        =   4
         Top             =   2130
         Width           =   1485
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
         TabIndex        =   3
         Top             =   2130
         Width           =   885
      End
   End
   Begin MSComctlLib.StatusBar sbBarraEstado 
      Align           =   2  'Align Bottom
      Height          =   285
      Left            =   0
      TabIndex        =   1
      Top             =   3450
      Width           =   9915
      _ExtentX        =   17489
      _ExtentY        =   503
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   5
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   8819
            MinWidth        =   8819
            Text            =   "Módulo de Cierre de Día"
            TextSave        =   "Módulo de Cierre de Día"
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
End
Attribute VB_Name = "frmPolizas"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private bCerrarForm As Boolean
Private dNoRegs As Long, dMonto As Double
Private sIdentificador As String
Private dFecha As Date
Private dFecComp As Date
Private sDescPoliza As String

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

Private Sub cmdCierre_Click()
Dim res As Variant
Dim sCadenaSQL As String
Dim acmd As New ADODB.Command
Dim oRstMov As New clsoAdoRecordset
Dim oRstObtPago As New clsoAdoRecordset, oRstObtRes As New clsoAdoRecordset
Dim iDelete As Integer

lblMensaje2.Caption = ""
dFecComp = FECHA_CALC(2)

    Select Case nPoliza
        Case 1  ' DESEMBOLSO DIARIO

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_DESEMBOLSO('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaDesemDia", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Desembolso Diario.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 2  ' DEVENGO DIARIO

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_DEVENGO_DIARIO('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaDevengoDia", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Devengo Diario.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 3  ' CHEQUES DIARIO
            
            If (oRstMov.Estado = adStateOpen) Then oRstMov.Cerrar
              
            sCadenaSQL = ""
            sCadenaSQL = sCadenaSQL & "SELECT * "
            sCadenaSQL = sCadenaSQL & "FROM PAG_GAR_SIM "
            sCadenaSQL = sCadenaSQL & "WHERE CDGEM = 'EMPFIN' "
            sCadenaSQL = sCadenaSQL & "AND FPAGO = '" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "' "
            sCadenaSQL = sCadenaSQL & "AND CLNS = 'G' "
            sCadenaSQL = sCadenaSQL & "AND ESTATUS IN ('DE', 'DS', 'DC') "
            sCadenaSQL = sCadenaSQL & "AND NOCHEQUE IS NULL "
            sCadenaSQL = sCadenaSQL & "AND FPAGO >= (SELECT MIN(FPAGO) FROM PAG_GAR_SIM WHERE CDGEM = 'EMPFIN' AND NOCHEQUE IS NOT NULL AND CDGCL IS NOT NULL)"
    
            If (oRstMov.Estado = adStateOpen) Then oRstMov.Cerrar
                oRstMov.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

            Select Case oRstMov.HayRegistros
                Case 1   '-----   La consulta SI retorno registros.   -----
           
                    MsgBox "No es posible generar la Póliza debido a que existen cheques de devolución de Garantía pendientes de imprimir.", vbCritical
                    oRstMov.Cerrar
                    Exit Sub
            
                End Select
            
            If (oRstMov.Estado = adStateOpen) Then oRstMov.Cerrar
              
            sCadenaSQL = ""
            sCadenaSQL = sCadenaSQL & "SELECT * "
            sCadenaSQL = sCadenaSQL & "FROM PRN, PRC "
            sCadenaSQL = sCadenaSQL & "WHERE PRN.CDGEM = 'EMPFIN' "
            sCadenaSQL = sCadenaSQL & "AND PRN.INICIO = '" & Format(DTPCalendario.Value, "dd/mm/yyyy") & "' "
            sCadenaSQL = sCadenaSQL & "AND PRC.CDGEM = PRN.CDGEM "
            sCadenaSQL = sCadenaSQL & "AND PRC.CDGNS = PRN.CDGNS "
            sCadenaSQL = sCadenaSQL & "AND PRC.CICLO = PRN.CICLO "
            sCadenaSQL = sCadenaSQL & "AND PRC.NOCHEQUE IS NULL"
    
            If (oRstMov.Estado = adStateOpen) Then oRstMov.Cerrar
                oRstMov.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

            Select Case oRstMov.HayRegistros
                Case 1   '-----   La consulta SI retorno registros.   -----
           
                    MsgBox "Existen cheques de desembolso pendientes de imprimir.", vbCritical
                    oRstMov.Cerrar
            
                End Select
            
            
            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_CHEQUES('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaChequesDia", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Cheques Diario.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 4  ' PAGOS DIARIO

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_PAGOS('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaPagosDia", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Pagos Diario.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 5  ' CHEQUES CANCELADOS

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_CHEQUES_CANCELADOS('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaChequesCancelados", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Cheques Cancelados.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 6  ' DEVOLUCION PARCIAL/TOTAL

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_DEVOLUCION('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaDevolucion", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Devolución Parcial/Total.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 7  ' AJUSTES

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_AJUSTES('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaAjustes", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Ajustes.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 20  ' VENTA DE SEGUROS

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_VTA_SEG_VIDA('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaVtaSegVida", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Venta de Seguro.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 21  ' COSTO DE SEGUROS

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_CTO_VTA_MES('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaCtoVtaSegVida", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Costo por Venta de Seguro.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 22  ' CANCELACION DE VENTA DE SEGUROS POR DEVOLUCION DE CREDITO

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_CANC_VTA_SEG_VIDA('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaCancVtaSegVida", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Cancelación de Venta de Seguro - Devolución.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
        Case 23  ' CANCELACION DE COSTO DE SEGUROS POR DEVOLUCION DE CREDITO

            Set acmd = Nothing
            With acmd
                .CommandText = "SP_POLIZA_CANC_CTO_VTA_MES('EMPFIN','" & Format(DTPCalendario.Value, "yyyy/mm/dd") & "','" & sUsuarioApp & "','" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "')"
                .CommandType = adCmdStoredProc
                .ActiveConnection = oAccesoDatos.cnn.ObjConexion
            End With
            acmd.Execute
            
            If CreaPolizaContable("PolizaCancCtoVtaSegVida", Format(DTPCalendario.Value, "dd/mmm/yyyy"), True) Then
             MsgBox "Se realizó exitosamente la generación de Póliza de Cancelación de Costo de Venta de Seguro - Devolución.", vbInformation
            Else
             MsgBox "No hay información para generar la Póliza.", vbCritical
             Exit Sub
            End If
    End Select


'Screen.MousePointer = vbDefault
'MsgBox "El Cierre de Día " & DTPCalendario.Value & " ha sido Procesado.", vbInformation + vbOKOnly, "Cierre de Día"
DTPCalendario.Enabled = True

lblMensaje2.Caption = "Proceso Terminado!"
'sbBarraEstado.Panels(1).Text = "Módulo de Cierre de Día"
cmdCierre.Enabled = True
cmdCerrar.Enabled = True

End Sub

Private Sub DTPCalendario_CloseUp()

'    If DTPCalendario.Value >= dFecha Then
'        MsgBox "La fecha seleccionada para el Proceso no puede ser igual o mayor a la fecha actual.", vbExclamation + vbOKOnly, "Cierre de Día"
'        DTPCalendario.Value = dFecha - 1
'    End If

End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    bCerrarForm = False
    cmdCierre.Visible = True
    lblMensaje.Caption = lblMensaje.Caption
    lblMensaje2.Caption = ""
    dFecha = FECHA_CALC(1)
    DTPCalendario.Value = dFecha - 1
    
    Select Case nPoliza
        Case 1
            sDescPoliza = "Póliza de Desembolso Diario"
        Case 2
            sDescPoliza = "Póliza de Devengo Diario"
        Case 3
            sDescPoliza = "Póliza de Cheques Diario"
        Case 4
            sDescPoliza = "Póliza de Pagos Diario"
        Case 5
            sDescPoliza = "Póliza de Cheques Cancelados"
        Case 6
            sDescPoliza = "Póliza de Devolución Parcial/Total"
        Case 7
            sDescPoliza = "Póliza de Ajustes"
        Case 20
            sDescPoliza = "Póliza de Venta de Seguro"
        Case 21
            sDescPoliza = "Póliza de Costo por Venta de Seguro"
        Case 22
            sDescPoliza = "Póliza de Canc. de Vta. de Seguro por Dev."
        Case 23
            sDescPoliza = "Póliza de Canc. de Cto. de Vta. de Seguro por Dev."
    End Select

    sbBarraEstado.Panels(1).Text = sDescPoliza
    Me.Caption = sDescPoliza
    Label4.Caption = sDescPoliza
    Frame1.Caption = sDescPoliza
    lblMensaje.Caption = sDescPoliza
    
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

Private Function FECHA_CALC(ByVal tipo As Integer) As Date
    Dim oRstPago As New clsoAdoRecordset

    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
              
    sCadenaSQL = ""
    If tipo = 1 Then
        sCadenaSQL = sCadenaSQL & "SELECT TRUNC(SYSDATE) FROM DUAL"
    ElseIf tipo = 2 Then
        sCadenaSQL = sCadenaSQL & "SELECT SYSDATE FROM DUAL"
    End If
    
    If (oRstPago.Estado = adStateOpen) Then oRstPago.Cerrar
    oRstPago.Abrir sCadenaSQL, oAccesoDatos.cnn.ObjConexion, adOpenKeyset, adLockReadOnly

    Select Case oRstPago.HayRegistros
        Case 1   '-----   La consulta SI retorno registros.   -----
           
            FECHA_CALC = oRstPago.ObjSetRegistros.Fields(0)
            oRstPago.Cerrar
            
    End Select
End Function

Private Function CreaPolizaContable(pNomArch As String, pFecha As Date, Optional pVentanaGuardar As Boolean) As Boolean
                                   
 ' OBJETIVO: Creación de Pólizas Contables
 ' PARAMETROS ENTRADA:  pTabla    - Nombre de la tabla q contiene la información
 '                      pNomArch  - Nombre del Archivo plano de salida
 '                      pModo     - Modo de escritura del Archivo APP ó OUT
 '                      pFECHA    - Fecha de Proceso
 '                      pPosicion - Numero de campo que será leido,
 '                      pSucursal - Numero de Sucursal (0-Todas)
 ' SALIDA : FALSE - Creación de Póliza Contable con error
 '           TRUE - Creación de Póliza Contable exitosa
 ' CREACION: AGG:11-abr-2007
 Dim sCadena As String
 Dim rs As ADODB.Recordset
 Dim cmd As ADODB.Command
 Dim strSuc As String
 Dim hFile As Integer
 Dim sPath As String

 
On Error GoTo RutinaError
    CreaPolizaContable = False
    hFile = FreeFile
    Set rs = New ADODB.Recordset
    Set cmd = New ADODB.Command
    
    'cmd.ActiveConnection = oAccesoDatos.cnn.ObjConexion
    'cn.BeginTrans
    
    If rs.State = adStateOpen Then rs.Close
    
        sCadena = " Select * from LAYOUTPOLIZAS WHERE TIPO = " & CStr(nPoliza) & " AND CDGPE = '" & sUsuarioApp & "' AND FREGISTRO = '" & Format(dFecComp, "yyyy/mm/dd hh:mm:ss") & "' ORDER BY ORDEN ASC"
        rs.Open sCadena, oAccesoDatos.cnn.ObjConexion, adOpenKeyset
    'cn.CommitTrans

    If rs.RecordCount > 1 Then
        sPath = App.Path & "\" & pNomArch & Format(pFecha, "ddmmyyyy") & ".txt"
        
        If pVentanaGuardar = True Then
            Me.cmmPolizas.Filter = "Archivos de texto|*.txt"
            Me.cmmPolizas.InitDir = sPath
            Me.cmmPolizas.FileName = sPath
            Me.cmmPolizas.DialogTitle = "Seleccione la ubicación para crear la póliza"
            Me.cmmPolizas.ShowSave
            sPath = Me.cmmPolizas.FileName
        End If
        
        Open sPath For Output As #hFile
        
        rs.MoveFirst
        Do While Not rs.EOF
            Print #hFile, rs.Fields(1).Value & vbCrLf;
            rs.MoveNext
        Loop
        Close #hFile
        rs.Close
    Else
        CreaPolizaContable = False
        Exit Function
    End If
    

CreaPolizaContable = True
Exit Function
RutinaError:
    If rs.State = adStateOpen Then rs.Close
    If Err.Number = 32755 Then
        Exit Function
    Else
        'cn.RollbackTrans
        MsgBox "Se ha generado el Error no: " & CStr(Err.Number) & vbNewLine & "Descripción: " & Err.Description, vbCritical + vbOKOnly, "Error en la Aplicación"
    End If
End Function

