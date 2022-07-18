# Installation

## Requirements
* Neteye (>= 4.15)
* Icinga Web 2 modules:
	* Auditlog

## Installation

For the configuration, `rpm-functions.sh` and `functions.sh` provided by `neteye_secure_install` will be used.

```bash
source /usr/share/neteye/secure_install/functions.sh
source /usr/share/neteye/scripts/rpm-functions.sh
```

Declaring common variables and creating passwords

```bash
MODULE=customactions
DB_PASSWORD=$(generate_and_save_pw customactions_db)
CONFDIR=/neteye/shared/icingaweb2/conf/modules/customactions
API_USER_DIR=/neteye/shared/icinga2/conf/icinga2/conf.d
API_PASSWORD=$(generate_and_save_pw customactions_api)
MODULE_DIR="/usr/share/icingaweb2/modules"
TARGET_DIR="${MODULE_DIR}/${MODULE}"
```

Clone the repository to your local system and configure it

```
Clone from bitbucket:
git clone https://pb00162@bitbucket.org/siwuerthphoenix/icingaweb2-module-customactions.git
Clone fomr Git:
git clone https://github.com/WuerthPhoenix/icingaweb2-module-customactions.git
mv icingaweb2-module-customactions/ ${TARGET_DIR}
cd ${TARGET_DIR}
chmod 755 ${TARGET_DIR}
chown apache:root ${TARGET_DIR}
```

Put Extra Lib-File:
```
mv LocalDateTimeElement.php /usr/share/icingaweb2/modules/ipl/vendor/ipl/html/src/FormElement/
```

Creating database and preparing it for access

```bash
cat <<EOF | mysql
CREATE DATABASE $MODULE;
CREATE USER '${MODULE}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER '${MODULE}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${MODULE}.* TO  TO '${MODULE}'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON ${MODULE}.* TO  TO '${MODULE}'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF
```

Importing last available schema

```bash
mysql ${MODULE} < ${TARGET_DIR}/etc/schema/mysql.schema.sql
```

Create IcingaWeb2 Resource for database customactions and enabling module

```bash
create_icingaweb2_db_resource ${MODULE} ${DB_PASSWORD}
icingacli module enable ${MODULE}
```


Configuring customactions api user

```bash
cat <<EOF > ${API_USER_DIR}/${MODULE}-user.conf
/**
 * The APIUser objects are used for authentication against the API.
 */
object ApiUser "${MODULE}" {
  password = "${API_PASSWORD}"
  // client_cn = ""

  permissions = [ "*" ]
}
EOF

chmod 640 ${API_USER_DIR}/${MODULE}-user.conf
chown icinga:icinga ${API_USER_DIR}/${MODULE}-user.conf
```

Configuring api user resource in modules config file for api access
Once created the icinga2 api user, restart the icinga2-master service

```
systemctl restart icinga2-master.service

```

Install the config file:
```
install -d -o apache -g icingaweb2 -m 770 "${CONFDIR}"
cd ${CONFDIR}
touch config.ini
```

Copy the following content into config.ini:
```
[apiuser]
host = "icinga2-master.neteyelocal"
port = "5665"
username = "${MODULE}"
password = "${API_PASSWORD}"
EOF
```
Currently you have to use the username and password of the director-api-user which is located in the file `/neteye/shared/icinga2/conf/icinga2/conf.d/director-user.conf`.

I'm not sure if the chown and chmod commands are neccessary.
```bash
chmod 660 ${CONFDIR}/config.ini;
chown apache:icingaweb2 ${CONFDIR}/config.ini
```

Restart the php-fpm service and check if is still running
```bash
systemctl restart php-fpm.service
systemctl status php-fpm.service
```

## Test
1. Open your Neteye-Web-Interface
2. Authenticate to enter
3. Check if on the left menu the module "Custom Actions" appears.
4. Click on it, generate a category and filter and try to schedule a downtime.
5. If scheduled successfully without an api error, everything works as it should.
