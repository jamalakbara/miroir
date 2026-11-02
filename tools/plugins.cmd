@echo off
rem Windows (PowerShell or cmd): runs tools/plugins.sh with Git Bash. Same arguments.
call "%~dp0lib\gitbash.cmd" "%~dp0plugins.sh" %*
