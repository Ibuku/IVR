[new]
exten => 39604,1,Verbose(1, Extension 39604)
exten => 39604,2,Progress()
exten => 39604,3,Answer()
exten => 39604,4,AGI(entry.php)
exten => 39604,5,Playback(incorrect)

exten => 39604,6,Playback(${play_path})
exten => 39604,7,AGI(module.php)

exten => 39604,n,Background(defaults/subscribe)
exten => 39604,n,GotoIf(condition?label1:label2)

exten => 39603,1,Background(defaults/confirmation)
exten => 39603,n,GotoIf(condition?label1:label2)

exten => 39604,n,AGI(subscribe.php)
exten => 39604,n,Playback(${response_path})
exten => 39604,n,Hangup()