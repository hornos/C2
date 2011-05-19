#!/bin/bash
# header
# ------------------------------------------------
SHPAK="${HOME}/shpak"
if ! test -d "${SHPAK}" ; then
  echo "No shpak"
  exit 1
fi
. ${SHPAK}/lib/h.sh

name="${sp_g_bn%%.sh}"
php="${name}.php"
lck="${name}.lck"
force=false

while getopts "f" opt; do
  case $opt in
    f)
      $force=true
    ;;
  esac
done

if ! test -x "${php}" ; then
  echo "${php} is not executable"
  exit 1
fi

if ${force} ; then
  sp_f_rmlck "${lck}"
fi

if ! sp_f_mklck "${lck}" ; then
  echo "${php} is locked"
  exit 1
fi

./${php}
_r=$?

sp_f_rmlck "${lck}"

exit ${_r}
