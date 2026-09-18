import json, subprocess

with open("/run/secrets/settings", "r") as settings:
	print("opening secrets")
	data = settings.read()
	settings_data = json.loads(data)
with open("/var/www/html/.env", "r") as env_file:
	print("opening env")
	env_data = env_file.read()
env_list = env_data.split("\n")
with open("/var/www/html/.env", "w") as out:
	for X in env_list:
		setting = X.split("=")[0]
		if setting in settings_data:
			print("found " +setting+ " setting to " +settings_data[setting]) 
			out.write(setting + "=" + settings_data[setting] + "\n")
		else:
			print(X + " no replacement needed")
			out.write(X + "\n")
print("starting httpd")
subprocess.call(["/usr/sbin/httpd", "-D", "FOREGROUND"])
