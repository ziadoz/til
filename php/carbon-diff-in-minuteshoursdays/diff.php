<?php
> \Carbon\Carbon::now()->subMinutes(60)->longAbsoluteDiffForHumans();
= "1 hour"

> \Carbon\Carbon::now()->subMinutes(1440)->longAbsoluteDiffForHumans();
= "1 day"

> \Carbon\Carbon::now()->subMinutes(1560)->longAbsoluteDiffForHumans();
= "1 day"
