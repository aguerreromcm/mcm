Attribute VB_Name = "iniFiles"
'--- Declaraciones para leer ficheros INI ---
'
' Leer todas las secciones de un fichero INI, esto seguramente no funciona en Win95
' *** Esta función no estaba en las declaraciones del API que se incluye con el VB ***
Private Declare Function GetPrivateProfileSectionNames Lib "kernel32" Alias "GetPrivateProfileSectionNamesA" _
    (ByVal lpszReturnBuffer As String, ByVal nSize As Long, _
    ByVal lpFileName As String) As Long
    
' Leer una sección completa
Private Declare Function GetPrivateProfileSection Lib "kernel32" Alias "GetPrivateProfileSectionA" _
    (ByVal lpAppName As String, ByVal lpReturnedString As String, _
    ByVal nSize As Long, ByVal lpFileName As String) As Long

' Leer una clave de un fichero INI
Private Declare Function GetPrivateProfileString Lib "kernel32" Alias "GetPrivateProfileStringA" _
    (ByVal lpApplicationName As String, ByVal lpKeyName As Any, _
     ByVal lpDefault As String, ByVal lpReturnedString As String, _
     ByVal nSize As Long, ByVal lpFileName As String) As Long

' Escribir una clave de un fichero INI (también para borrar claves y secciones)
Private Declare Function WritePrivateProfileString Lib "kernel32" Alias "WritePrivateProfileStringA" _
    (ByVal lpApplicationName As String, ByVal lpKeyName As Any, _
     ByVal lpString As Any, ByVal lpFileName As String) As Long
     
Enum cveInstrumento
    Balanza = 1
    Manometro = 2
    Ctrltemperatura = 3
    Dimensional = 4
    PH = 5
    conduct = 6
    TemperaturaHumedad = 7
    SensoresTemperatura = 8
    PresionDiferencial = 9
    Graficadores = 10
    TiempoFrecuencia = 11
End Enum
Public rutaBaseDatos As String
Public rutaReportes As String
Dim decRepetibilidad As Integer
Dim decLinealidad As Integer
Public UltCal As Date
Public Periodo As Integer
Public proxcal As Date


Public Function IniGet(ByVal lpFileName As String, ByVal lpAppName As String, _
                       ByVal lpKeyName As String, _
                       Optional ByVal lpDefault As String = "") As String
    '
    'Los parámetros son:
    'lpFileName:    La Aplicación (fichero INI)
    'lpAppName:     La sección que suele estar entrre corchetes
    'lpKeyName:     Clave
    'lpDefault:     Valor opcional que devolverá si no se encuentra la clave.
    '
    Dim LTmp As Long
    Dim sRetVal As String
    
    sRetVal = String$(255, 0)
    
    LTmp = GetPrivateProfileString(lpAppName, lpKeyName, lpDefault, sRetVal, Len(sRetVal), lpFileName)
    If LTmp = 0 Then
        IniGet = lpDefault
    Else
        IniGet = Left(sRetVal, LTmp)
    End If
End Function


Public Sub IniWrite(ByVal lpFileName As String, ByVal lpAppName As String, _
                    ByVal lpKeyName As String, ByVal lpString As String)
    '
    'Guarda los datos de configuración
    'Los parámetros son los mismos que en IniGet
    'Siendo lpString el valor a guardar
    '

    Call WritePrivateProfileString(lpAppName, lpKeyName, lpString, lpFileName)
End Sub


Private Sub IniDelete(ByVal sIniFile As String, ByVal sSection As String, _
                    Optional ByVal sKey As String = "")
    '
    ' Borrar una clave o entrada de un fichero INI                      (16/Feb/99)
    ' Si no se indica sKey, se borrará la sección indicada en sSection
    ' En otro caso, se supone que es la entrada (clave) lo que se quiere borrar
    '
    If Len(sKey) = 0 Then
        ' Borrar una sección
        Call WritePrivateProfileString(sSection, 0&, 0&, sIniFile)
    Else
        ' Borrar una entrada
        Call WritePrivateProfileString(sSection, sKey, 0&, sIniFile)
    End If
End Sub


Private Function IniGetSection(ByVal lpFileName As String, _
                              ByVal lpAppName As String) As Variant
    '
    ' Lee una sección entera de un fichero INI                          (27/Feb/99)
    '
    ' Usando Collection en lugar de cParrafos y cContenido              (06/Mar/99)
    '
    ' Esta función devolverá una colección con cada una de las claves y valores
    ' que haya en esa sección.
    ' Parámetros de entrada:
    '   lpFileName  Nombre del fichero INI
    '   lpAppName   Nombre de la sección a leer
    ' Devuelve:
    '   Una colección con el Valor y el contenido
    '   Para leer los datos:
    '       For i = 1 To tContenidos Step 2
    '           sClave = tContenidos(i)
    '           sValor = tContenidos(i+1)
    '       Next
    '
    Dim tContenidos As Collection
    Dim nSize As Long
    Dim i As Long
    Dim j As Long
    Dim sTmp As String
    Dim sClave As String
    Dim sValor As String
    
    
    ' El tamaño máximo para Windows 95
    sBuffer = String$(32767, Chr$(0))
    
    nSize = GetPrivateProfileSection(lpAppName, sBuffer, Len(sBuffer), lpFileName)
        
    If nSize Then
        Set tContenidos = New Collection
        
        ' Cortar la cadena al número de caracteres devueltos
        sBuffer = Left$(sBuffer, nSize)
        ' Quitar los vbNullChar extras del final
        i = InStr(sBuffer, vbNullChar & vbNullChar)
        If i Then
            sBuffer = Left$(sBuffer, i - 1)
        End If
        
        ' Cada una de las entradas estará separada por un Chr$(0)
        Do
            i = InStr(sBuffer, Chr$(0))
            If i Then
                sTmp = LTrim$(Left$(sBuffer, i - 1))
                If Len(sTmp) Then
                    ' Comprobar si tiene el signo igual
                    j = InStr(sTmp, "=")
                    If j Then
                        sClave = Left$(sTmp, j - 1)
                        sValor = LTrim$(Mid$(sTmp, j + 1))
                        ' Asignar la clave y el valor
                        tContenidos.Add sClave
                        tContenidos.Add sValor
                    End If
                End If
                sBuffer = Mid$(sBuffer, i + 1)
            End If
        Loop While i
        ' Por si aún queda algo...
        If Len(sBuffer) Then
            j = InStr(sBuffer, "=")
            If j Then
                sClave = Left$(sBuffer, j - 1)
                sValor = LTrim$(Mid$(sBuffer, j + 1))
                tContenidos.Add sClave
                tContenidos.Add sValor
            End If
        End If
    End If
    Set IniGetSection = tContenidos
End Function


Private Function IniGetSections(ByVal lpFileName As String) As Variant
    '
    ' Devuelve todas las secciones de un fichero INI
    '
    ' Usando Collection en lugar de cParrafos y cContenido
    '
    ' Esta función devolverá una colección con todas las secciones del fichero
    ' Parámetros de entrada:
    '   lpFileName  Nombre del fichero INI
    ' Devuelve:
    '   Una colección con los nombres de las secciones
    '
    Dim tContenidos As Collection
    Dim nSize As Long
    Dim i As Long
    Dim sTmp As String
    
    ' El tamaño máximo para Windows 95
    sBuffer = String$(32767, Chr$(0))
    
    ' Esta función del API no está definida en el fichero TXT
    nSize = GetPrivateProfileSectionNames(sBuffer, Len(sBuffer), lpFileName)
        
    If nSize Then
        ' Crear una colección del tipo cParrafos que es una colección
        ' con elementos del tipo cContenido
        Set tContenidos = New Collection
        
        ' Cortar la cadena al número de caracteres devueltos
        sBuffer = Left$(sBuffer, nSize)
        ' Quitar los vbNullChar extras del final
        i = InStr(sBuffer, vbNullChar & vbNullChar)
        If i Then
            sBuffer = Left$(sBuffer, i - 1)
        End If
        
        ' Cada una de las entradas estará separada por un Chr$(0)
        Do
            i = InStr(sBuffer, Chr$(0))
            If i Then
                sTmp = LTrim$(Left$(sBuffer, i - 1))
                If Len(sTmp) Then
                    tContenidos.Add sTmp
                End If
                sBuffer = Mid$(sBuffer, i + 1)
            End If
        Loop While i
        If Len(sBuffer) Then
            tContenidos.Add sBuffer
        End If
    End If
    Set IniGetSections = tContenidos
End Function

