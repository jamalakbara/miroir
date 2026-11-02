@echo off
rem Run a tools/*.sh script with Git for Windows' bash. In PowerShell and cmd a plain
rem "bash" is WSL's, which cannot reach Docker or this checkout the same way.
rem Usage (from a wrapper): call "%~dp0lib\gitbash.cmd" <script.sh> [args...]
setlocal
set "GITBASH="
for /f "delims=" %%G in ('where git 2^>nul') do (
	if not defined GITBASH if exist "%%~dpG..\bin\bash.exe" set "GITBASH=%%~dpG..\bin\bash.exe"
	if not defined GITBASH if exist "%%~dpG..\..\bin\bash.exe" set "GITBASH=%%~dpG..\..\bin\bash.exe"
)
if not defined GITBASH if exist "%ProgramFiles%\Git\bin\bash.exe" set "GITBASH=%ProgramFiles%\Git\bin\bash.exe"
if not defined GITBASH (
	echo error: Git Bash not found. Install Git for Windows from https://git-scm.com 1>&2
	exit /b 1
)
rem Forward slashes, so the script's own "cd $(dirname $0)/.." works in bash.
set "SCRIPT=%~1"
set "SCRIPT=%SCRIPT:\=/%"
shift
set "ARGS="
:args
if "%~1"=="" goto run
set "ARGS=%ARGS% %1"
shift
goto args
:run
"%GITBASH%" "%SCRIPT%"%ARGS%
exit /b %ERRORLEVEL%
