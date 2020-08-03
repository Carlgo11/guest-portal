# UniFi Guest Portal

## Usage

```BASH
docker run -p 80:80 carlgo11/guest-portal
```

Optionally, add some background images:

```BASH
docker run -p 80:80 -v $(pwd)/bg.webp:/opt/www/img/bg.webp -v $(pwd)/bg.jpg:/opt/www/img/bg.jpg carlgo11/guest-portal
```

## Environment variables

|Name|Default|Description|Example|
|----|-------|-----------|-------|
|UNIFI_USER| |UniFi Hotspot username|api
|UNIFI_PASSWORD| |UniFi Hotspot password|password
|UNIFI_URL| |UniFi Controller IP/URL & port|https://192.168.1.2:8443
|UNIFI_SITE|default|UniFi Site|default
|UNIFI_VERSION| |Controller version|5.13.32

## License

This work is licensed under the Creative Commons Attribution 4.0 International License.  
To view a copy of this license, visit [LICENSE](LICENSE).
