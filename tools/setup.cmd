@echo off
rem Windows (PowerShell or cmd): runs tools/setup.sh with Git Bash. Same arguments.
call "%~dp0lib\gitbash.cmd" "%~dp0setup.sh" %*
