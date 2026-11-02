@echo off
rem Windows (PowerShell or cmd): runs tools/snapshot.sh with Git Bash. Same arguments.
call "%~dp0lib\gitbash.cmd" "%~dp0snapshot.sh" %*
