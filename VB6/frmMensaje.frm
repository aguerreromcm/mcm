VERSION 5.00
Begin VB.Form frmMensaje 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Módulo de importación y conciliación de pagos"
   ClientHeight    =   3390
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   6465
   ControlBox      =   0   'False
   Icon            =   "frmMensaje.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3390
   ScaleWidth      =   6465
   StartUpPosition =   2  'CenterScreen
   Begin AdminCred.ctlMensajeImp ctlMensajeImp 
      Height          =   3330
      Left            =   30
      TabIndex        =   0
      Top             =   0
      Width           =   6405
      _ExtentX        =   11298
      _ExtentY        =   5874
   End
End
Attribute VB_Name = "frmMensaje"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub ctlMensajeImp_ClickCancelar()
    Dim res
    
    'res = MsgBox("¿Esta seguro(a) que desea cancelar la importación de pagos?", vbQuestion + vbYesNo, TITULO_MENSAJE)
    'If res = vbYes Then
        With cPagoImp
            .Cancelar = True
        End With
    'End If
    
    Unload Me
End Sub

Private Sub ctlMensajeImp_ClickNo()
    With cPagoImp
        .No = True
    End With
    
    Unload Me
End Sub

Private Sub ctlMensajeImp_ClickNoATodos()
    With cPagoImp
        .NoATodos = True
    End With
    
    Unload Me
End Sub

Private Sub ctlMensajeImp_ClickSi()
    With cPagoImp
        .Si = True
    End With

    Unload Me
End Sub

Private Sub ctlMensajeImp_ClickSiATodos()
    With cPagoImp
        .SiATodos = True
    End With

    Unload Me
End Sub

Private Sub Form_Load()
    With ctlMensajeImp
        ctlMensajeImp.Fecha = Format(sFechaImp, "dd/mm/yyyy")
        ctlMensajeImp.Referencia = sRefIM
        ctlMensajeImp.Monto = Format(dMontoPago, "$ ###,###,###,##0.00")
        cPagoImp.Cancelar = False
        cPagoImp.No = False
        cPagoImp.NoATodos = False
        cPagoImp.Preguntar = True
        cPagoImp.Si = False
        cPagoImp.SiATodos = False
    End With
End Sub

Private Sub Form_Resize()
    Me.Height = 3870
    Me.Width = 6560
End Sub
