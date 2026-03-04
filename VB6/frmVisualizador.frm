VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Begin VB.Form frmVisualizador 
   Caption         =   "Vista previa del reporte"
   ClientHeight    =   11610
   ClientLeft      =   60
   ClientTop       =   450
   ClientWidth     =   12210
   LinkTopic       =   "Form1"
   MinButton       =   0   'False
   ScaleHeight     =   11610
   ScaleWidth      =   12210
   StartUpPosition =   2  'CenterScreen
   WindowState     =   2  'Maximized
   Begin VB.PictureBox pbPie 
      Align           =   2  'Align Bottom
      Height          =   465
      Left            =   0
      ScaleHeight     =   405
      ScaleWidth      =   12150
      TabIndex        =   1
      Top             =   11145
      Width           =   12210
      Begin AdminCred.ctlBoton cmdCerrar 
         Height          =   270
         Left            =   10830
         TabIndex        =   2
         Top             =   90
         Width           =   1260
         _ExtentX        =   2223
         _ExtentY        =   476
      End
   End
   Begin CRVIEWERLibCtl.CRViewer CRVReporte 
      Height          =   8925
      Left            =   60
      TabIndex        =   0
      Top             =   60
      Width           =   12015
      DisplayGroupTree=   -1  'True
      DisplayToolbar  =   -1  'True
      EnableGroupTree =   -1  'True
      EnableNavigationControls=   -1  'True
      EnableStopButton=   -1  'True
      EnablePrintButton=   -1  'True
      EnableZoomControl=   -1  'True
      EnableCloseButton=   -1  'True
      EnableProgressControl=   -1  'True
      EnableSearchControl=   -1  'True
      EnableRefreshButton=   -1  'True
      EnableDrillDown =   -1  'True
      EnableAnimationControl=   -1  'True
      EnableSelectExpertButton=   0   'False
      EnableToolbar   =   -1  'True
      DisplayBorder   =   -1  'True
      DisplayTabs     =   -1  'True
      DisplayBackgroundEdge=   -1  'True
      SelectionFormula=   ""
      EnablePopupMenu =   -1  'True
      EnableExportButton=   0   'False
      EnableSearchExpertButton=   0   'False
      EnableHelpButton=   0   'False
   End
End
Attribute VB_Name = "frmVisualizador"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private bCerrarForm As Boolean

Private Sub cmdCerrar_BotonClick()
    bCerrarForm = True
    Unload Me
End Sub

Private Sub Form_Load()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    bCerrarForm = False
    cmdCerrar.Texto = "&Cerrar"
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

Private Sub Form_Resize()
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass

    If (Me.Height >= 2000 And Me.Width >= 2000) Then
        With CRVReporte
            .Left = 0
            .Top = 0
            .Height = Me.ScaleHeight - Me.pbPie.Height
            .Width = Me.ScaleWidth
        End With
        
        pbPie.Height = 465
        cmdCerrar.Left = Me.ScaleWidth - cmdCerrar.Width - 80
        cmdCerrar.Top = 50
    Else
       Me.Height = 3000
       Me.Width = 4000
'
'        With CRVReporte
'            .Left = 0
'            .Top = 0
'            .Height = Me.ScaleHeight - Me.pbPie.Height
'            .Width = Me.ScaleWidth
'        End With
'
'        pbPie.Height = 465
'        cmdCerrar.Left = Me.ScaleWidth - cmdCerrar.Width - 80
'        cmdCerrar.Top = 50
    End If

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
