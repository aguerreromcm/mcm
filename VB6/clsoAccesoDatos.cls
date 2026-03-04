VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsoAccesoDatos"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private meEstadoConexion As ObjectStateEnum
Public cnn As clsoAdoConexion
Public rst As clsoAdoRecordset
'''''Private Enum eResultadoQuery
'''''    eSinDatos = 0
'''''    eConDatos = 1
'''''    eFalloQuery = 2
'''''End Enum


Public Property Get EstadoConexion() As ObjectStateEnum
    EstadoConexion = meEstadoConexion
End Property

Public Sub Conectar(ByVal psAmbiente As String)
    Dim sDataSource As String, sUser As String, sPwd As String
    Dim sAmb As String
    
    On Error GoTo RutinaError
    Screen.MousePointer = vbHourglass
    
    sDataSource = IniGet(App.Path & "/Parametros.ini", "DataSource", "FuenteDatos", "")
    sUser = IniGet(App.Path & "/Parametros.ini", "Usuario", "user", "")
    sPwd = IniGet(App.Path & "/Parametros.ini", "Usuario", "pwd", "")
    
    Select Case psAmbiente
        Case "Cultiva"
            sDataSource = IniGet(App.Path & "/ParametrosSist.ini", "DataSourceTest", "FuenteDatos", "")
            sUser = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioTest", "user", "")
            sPwd = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioTest", "pwd", "")
        Case "MasConMenos"
            sDataSource = IniGet(App.Path & "/ParametrosSist.ini", "DataSourceProd", "FuenteDatos", "")
            sUser = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioProd", "user", "")
            sPwd = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioProd", "pwd", "")
        Case "Sistemas"
            sDataSource = IniGet(App.Path & "/ParametrosSist.ini", "DataSourceSystems", "FuenteDatos", "")
            sUser = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioProd", "user", "")
            sPwd = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioProd", "pwd", "")
    End Select
    
'''    If (psAmbiente = "Prueba") Then
'''        'sAmb = "esiacomtest"
'''
'''        sDataSource = IniGet(App.Path & "/ParametrosSist.ini", "DataSourceTest", "FuenteDatos", "")
'''        sUser = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioTest", "user", "")
'''        sPwd = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioTest", "pwd", "")
'''    Else
'''        'sAmb = "esiacom"
'''
'''        sDataSource = IniGet(App.Path & "/ParametrosSist.ini", "DataSourceProd", "FuenteDatos", "")
'''        sUser = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioProd", "user", "")
'''        sPwd = IniGet(App.Path & "/ParametrosSist.ini", "UsuarioProd", "pwd", "")
'''    End If
    
    'cnn.Abrir "Provider=OraOLEDB.Oracle;Data Source=" & sAmb & ";User Id=esiacom;Password=FNS030101"
    cnn.Abrir "Provider=OraOLEDB.Oracle;Data Source=" & sDataSource & ";User Id=" & sUser & ";Password=" & sPwd
    
    Select Case cnn.Estado
        Case 0      '-----   cerrado              -----
            meEstadoConexion = adStateClosed
        Case 1      '-----   abierto              -----
            meEstadoConexion = adStateOpen
        Case 2      '-----   conectando           -----
            meEstadoConexion = adStateConnecting
        Case 4      '-----   ejecutando comando   -----
            meEstadoConexion = adStateExecuting
        Case 8      '-----   obteniendo regs.     -----
            meEstadoConexion = adStateFetching
    End Select
    
    Screen.MousePointer = vbDefault
    Exit Sub
RutinaError:
    MensajeError Err
End Sub

'''''Public Function Ejecutar() As Boolean
'''''    On Error GoTo RutinaError
'''''    Screen.MousePointer = vbHourglass
'''''    Ejecutar = False
'''''
'''''    Cnn.Ejecutar psCadenaSQL
'''''
'''''    Ejecutar = True
'''''    Screen.MousePointer = vbDefault
'''''    Exit Function
'''''RutinaError:
'''''    MensajeError Err
'''''End Function
'''''
'''''Public Function Consultar(Optional ByVal psCadenaSQL As String, Optional ByVal peTipoCursor As CursorTypeEnum, Optional ByVal peTipoCerrojo As LockTypeEnum) As Boolean
'''''    On Error GoTo RutinaError
'''''    Screen.MousePointer = vbHourglass
'''''    Consultar = False
'''''
'''''    Rst.Abrir psCadenaSQL, Cnn.ObjConexion, peTipoCursor, peTipoCerrojo
'''''
'''''    Consultar = True
'''''    Screen.MousePointer = vbDefault
'''''    Exit Function
'''''RutinaError:
'''''    MensajeError Err
'''''End Function
'''''
Private Sub Class_Initialize()
    Set cnn = New clsoAdoConexion
    Set rst = New clsoAdoRecordset
End Sub
