Configuración por entorno (opcional).
Si se define la variable de entorno APP_ENV (o $_ENV['APP_ENV']) y existe
un archivo con el mismo nombre aquí (ej. production.ini, local.ini),
se usará en lugar de configuracion.ini.
Si no hay variable o el archivo no existe, se usa configuracion.ini (fallback).
Estructura de los .ini: misma que configuracion.ini (clave = valor).
