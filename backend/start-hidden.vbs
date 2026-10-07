' Launch dazi backend components HIDDEN (no console windows):
'   5x php-cgi workers (FastCGI 9100-9104) + nginx (port 8080)
Set sh = CreateObject("WScript.Shell")
sh.CurrentDirectory = "E:\project\dazi\backend\nginx\nginx-1.24.0"
sh.Run "C:\tools\php74\php-cgi.exe -b 127.0.0.1:9100", 0, False
sh.Run "C:\tools\php74\php-cgi.exe -b 127.0.0.1:9101", 0, False
sh.Run "C:\tools\php74\php-cgi.exe -b 127.0.0.1:9102", 0, False
sh.Run "C:\tools\php74\php-cgi.exe -b 127.0.0.1:9103", 0, False
sh.Run "C:\tools\php74\php-cgi.exe -b 127.0.0.1:9104", 0, False
sh.Run "nginx.exe", 0, False
