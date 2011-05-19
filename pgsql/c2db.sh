#!/bin/bash

# header
# ------------------------------------------------
SHPAK="${HOME}/shpak"
if ! test -d "${SHPAK}" ; then
  echo "No shpak"
  exit 1
fi
. ${SHPAK}/lib/h.sh

sp_f_load sql/pg

# usage
# ------------------------------------------------
function __usage() {
  echo "Usage: ${sp_g_bn} -a app -c cmd"
}

# options
# ------------------------------------------------
if test $# -lt 1 ; then
  __usage
  exit 1
fi

cmd="l"
while getopts "c:ha:" opt; do
  case $opt in
    h)
      __usage
      exit 1
    ;;
    a)
      app=${OPTARG}
    ;;
    c)
      cmd=${OPTARG}
    ;;
  esac
done

app_dir="app/${app}"
if ! test -d "${app_dir}" ; then
  sp_f_err_fnf "${app_dir}"
  exit 1
fi

app_db="${app_dir}/DB.cfg"
if ! test -f "${app_db}" ; then
  sp_f_err_fnf "${app_db}"
  exit 1
fi
. ${app_db}


# login
# ------------------------------------------------
if test "${cmd}" = "l"; then
  sp_f_stt "Login to: ${PG_URL}"
  sp_f_pg "${PG_URL}"
  exit $?
fi

# init
# ------------------------------------------------
cd "${app_dir}"

tmp="tmp.sql"

dbinit=( "init.sql" "func.sql" "func.grant.sql" )
ttinit=( "init" "func" "grant" "data" )

if test "${cmd}" = "i"; then
  sp_f_stt "Create Database: ${PG_URL}"
  echo -e "\nWARNING: ALL DATA WILL BE LOST!"

  timestamp=`date`
  echo -e "\n--\n-- ${timestamp}\n--" > "${tmp}"

  for i in ${dbinit[@]} ; do
    if ! test -r "${i}" ; then
      sp_f_err_fnf "${i}"
      exit 1
    fi
    echo -e "\n--\n-- ${i}\n--" >> "${tmp}"
    cat "${i}"                  >> "${tmp}"
  done

  sp_f_yesno "I. Initialize the database?"
  sp_f_pg "${PG_URL}" "${tmp}"

  timestamp=`date`
  echo -e "\n--\n-- ${timestamp}\n--" > "${tmp}"

  for i in ${PG_TABLES[@]} ; do
    for t in ${ttinit[@]} ; do
      it="TABLE_${i}.${t}.sql"
      if ! test -r "${it}" ; then
        sp_f_err_fnf "${it}"
        exit 1
      fi
      echo -e "\n--\n-- ${it}\n--" >> "${tmp}"
      cat "${it}"                  >> "${tmp}"
    done
  done

  sp_f_yesno "II. Setup the database?"
  sp_f_pg "${PG_URL}" "${tmp}"
fi
