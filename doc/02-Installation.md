# Installation

## Requirements
* Neteye (>= 4.15)
* Icinga Web 2 modules:
	* Auditlog

## Installation

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

For the configuration, `rpm-functions.sh` and `functions.sh` provided by `neteye_secure_install` will be used.

```bash
source /usr/share/neteye/secure_install/functions.sh
source /usr/share/neteye/scripts/rpm-functions.sh
```

Clone the repository to your local system and configure it

```bash
cd ${MODULE_DIR}
git clone https://Dominik17@bitbucket.org/Dominik17/customactions.git
chmod 755 customactions
chown apache:root customactions
cd customactions/
mv LocalDateTimeElement.php /usr/share/icingaweb2/modules/ipl/vendor/ipl/html/src/FormElement/
```

Creating database and preparing it for access

```bash
cat <<EOF | mysql
CREATE DATABASE $MODULE;
GRANT SELECT, INSERT, UPDATE, DELETE, DROP, CREATE VIEW, INDEX, EXECUTE ON ${MODULE}.* TO '${MODULE}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT SELECT, INSERT, UPDATE, DELETE, DROP, CREATE VIEW, INDEX, EXECUTE ON ${MODULE}.* TO '${MODULE}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
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

a) Configuring api user resource in modules config file for api access

```bash
install -d -o apache -g icingaweb2 -m 770 "${CONFDIR}"

cat <<EOF > ${CONFDIR}/config.ini
[apiuser]
host = "icinga2-master.neteyelocal"
port = "5665"
username = "${MODULE}"
password = "${API_PASSWORD}"
EOF

chmod 660 ${CONFDIR}/config.ini
chown apache:icingaweb2 ${CONFDIR}/config.ini
```

b) Currently you have to make step 9 with username and password of the director-api-user in the file `/neteye/shared/icinga2/conf/icinga2/conf.d/director-user.conf`. Also I'm not sure if the chown and chmod commands are neccessary.

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
