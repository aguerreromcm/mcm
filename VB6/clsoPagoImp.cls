VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsoPagoImp"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private mbNo As Boolean
Private mbNoATodos As Boolean
Private mbSi As Boolean
Private mbSiATodos As Boolean
Private mbCancelar As Boolean
Private mbPreguntar As Boolean

Public Property Get Preguntar() As Boolean
    Preguntar = mbPreguntar
End Property

Public Property Let Preguntar(pbValor As Boolean)
    mbNo = False
    mbNoATodos = False
    mbSi = False
    mbSiATodos = False
    mbCancelar = False
    mbPreguntar = pbValor
End Property

Public Property Get No() As Boolean
    No = mbNo
End Property

Public Property Let No(pbValor As Boolean)
    mbNo = pbValor
    mbNoATodos = False
    mbSi = False
    mbSiATodos = False
    mbCancelar = False
    mbPreguntar = True
End Property

Public Property Get NoATodos() As Boolean
    NoATodos = mbNoATodos
End Property

Public Property Let NoATodos(pbValor As Boolean)
    mbNo = False
    mbNoATodos = pbValor
    mbSi = False
    mbSiATodos = False
    mbCancelar = False
    mbPreguntar = False
End Property

Public Property Get Si() As Boolean
    Si = mbSi
End Property

Public Property Let Si(pbValor As Boolean)
    mbNo = False
    mbNoATodos = False
    mbSi = pbValor
    mbSiATodos = False
    mbCancelar = False
    mbPreguntar = True
End Property

Public Property Get SiATodos() As Boolean
    SiATodos = mbSiATodos
End Property

Public Property Let SiATodos(pbValor As Boolean)
    mbNo = False
    mbNoATodos = False
    mbSi = False
    mbSiATodos = pbValor
    mbCancelar = False
    mbPreguntar = False
End Property

Public Property Get Cancelar() As Boolean
    Cancelar = mbCancelar
End Property

Public Property Let Cancelar(pbValor As Boolean)
    mbNo = False
    mbNoATodos = False
    mbSi = False
    mbSiATodos = False
    mbCancelar = pbValor
    mbPreguntar = False
End Property

Private Sub Class_Initialize()
    mbNo = False
    mbNoATodos = False
    mbSi = False
    mbSiATodos = False
    mbCancelar = False
    mbPreguntar = True
End Sub
