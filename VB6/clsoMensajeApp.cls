VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsoMensajeApp"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private meEstilo As VbMsgBoxStyle
Private meResultado As VbMsgBoxResult
Private msTitulo As String
Private mlAlto As Long
Private mlAncho As Long

Public Property Get Estilo() As VbMsgBoxStyle
    Estilo = meEstilo
End Property

Public Property Let Estilo(ByVal peValor As VbMsgBoxStyle)
    meEstilo = peValor
End Property

Public Property Get Resultado() As VbMsgBoxResult
    Resultado = meResultado
End Property

Public Property Get Titulo() As String
    Titulo = msTitulo
End Property

Public Property Let Titulo(ByVal psValor As String)
    msTitulo = psValor
End Property

Public Property Get Alto() As Long
    Alto = mlAlto
End Property

Public Property Let Alto(ByVal plValor As Long)
    mlAlto = plValor
End Property

Public Property Get Ancho() As Long
    Ancho = mlAncho
End Property

Public Property Let Ancho(ByVal plValor As Long)
    mlAncho = plValor
End Property

