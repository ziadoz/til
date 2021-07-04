SET /P state=Start (1) or Stop (0) Armoury Crate? 

@ECHO OFF

IF %state% == 1 (ECHO "Starting Armoury Crate..." & NET START ArmouryCrateControlInterface & NET START ArmouryCrateService) ELSE (ECHO "Stopping Armoury Crate..." & NET STOP ArmouryCrateControlInterface & NET STOP ArmouryCrateService)

ECHO "Done."

PAUSE