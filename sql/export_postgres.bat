@echo off
cd /d %~dp0
TITLE %~dpnx0

@SETLOCAL
set "TAB=   "
@SET drv=%~d0
@SET pth=%~p0
@SET fpath=%~dp0
@SET fname=%~n0
@SET ext=%~x0


@SET dbname="cs6400_sp17_team001"
@SET mysql_path="C:\Bitnami\wampstack-7.1.2-0\mysql\bin"
@SET output_path="C:\Bitnami\wampstack-7.1.2-0\apache2\htdocs\Phase3_Sample_Submission\sql"
@SET output_sql="DUMP_%dbname%_postgre.sql"

echo Running: %fpath%%fname%%ext% 

echo %mysql_path%\mysqldump.exe -u root -p --compatible=postgresql %dbname% > %~dp0\%output_sql%
%mysql_path%\mysqldump.exe -u root -p --compatible=postgresql %dbname% > %~dp0\%output_sql%
type %~dp0\%output_sql%

@ENDLOCAL
echo.
echo %fpath%%fname%%ext% complete...
timeout /t 5

