VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsoCtaBancaria"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private msEmpresa As String
Private msNoCta As String
Private msFechaPago As String
Private msTipoPagos As String

Public Property Get Empresa() As String
    Empresa = msEmpresa
End Property

Public Property Let Empresa(psValor As String)
    msEmpresa = psValor
End Property

Public Property Get NoCta() As String
    NoCta = msNoCta
End Property

Public Property Let NoCta(psValor As String)
    msNoCta = psValor
End Property

Public Property Get FechaPago() As String
    FechaPago = msFechaPago
End Property

Public Property Let FechaPago(psValor As String)
    msFechaPago = psValor
End Property

Public Property Get TipoPagos() As String
    TipoPagos = msTipoPagos
End Property

Public Property Let TipoPagos(psValor As String)
    msTipoPagos = psValor
End Property

Private Sub Class_Initialize()
    msEmpresa = ""
    msNoCta = ""
    msFechaPago = ""
    msTipoPagos = ""
End Sub
