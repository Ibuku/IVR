<?php

shell_exec('plink -v -x -a -T -C -noagent -ssh -L 127.0.0.1:8980:10.64.33.230:8079 root@campaign.atp-sevas.com');


