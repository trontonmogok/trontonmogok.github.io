git-hash-object () { # substitute when the `git` command is not available
    local type=blob
    [ "$1" = "-t" ] && shift && type=$1 && shift
    # depending on eol/autocrlf settings, you may want to substitute CRLFs by LFs
    # by using `perl -pe 's/\r$//g'` instead of `cat` in the next 2 commands
    local size=$(cat $1 | wc -c | sed 's/ .*$//')
    ( echo -en "$type $size\0"; cat "$1" ) | sha1sum | sed 's/ .*$//'
}
