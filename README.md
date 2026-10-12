# RegistrationDomainGuard

Ein Zusatz-Plugin für **plentyShop LTS**, das neue Registrierungen auf eine
konfigurierbare Liste von E-Mail-Domains beschränkt. Die Prüfung erfolgt
serverseitig vor dem Anlegen des Kontakts. Abgewiesene Registrierungen erhalten
eine Fehlermeldung im vorhandenen Registrierungsformular.

Es sind keine Firmennamen, Shop-Adressen oder freigegebenen Domains vorgegeben.

## Installation über Git

Repository: `https://github.com/edgardicked-hub/RegistrationDomainGuard.git`

Branch: **main**

1. Das Repository unter **Plugins → Git** hinzufügen.
2. Im gewünschten Plugin-Set unter **Plugin hinzufügen** das Repository und
   den Branch **main** installieren. Für den ersten Build eine Kopie des Sets verwenden.
3. **RegistrationDomainGuard → Konfiguration → Registrierung → Erlaubte
   E-Mail-Domains** öffnen und die gewünschten Domains eintragen.
4. Speichern, das Plugin aktivieren und das Plugin-Set vollständig bereitstellen.
5. Eine erlaubte und eine abgewiesene Registrierung im Test-Set prüfen.

`plugin.json` liegt direkt im Repository-Hauptverzeichnis. Alternativ den Ordner
**RegistrationDomainGuard** mit plentyDevTool in das gewünschte Set übertragen.
Ein ZIP-Upload im ShopBuilder installiert kein Plugin.

## Konfiguration

Es gibt ein Konfigurationsfeld: **Erlaubte E-Mail-Domains**. Beispiel:

```text
example.com
example.org
partner.example.net
```

Kommas, Semikolons, Leerzeichen und Zeilenumbrüche können die Domains trennen.
Ein führendes `@` ist erlaubt. Groß-/Kleinschreibung wird bei Domains ignoriert;
doppelte Einträge werden zusammengefasst.

- Es zählt die **vollständige Domain** hinter dem `@`.
- Subdomains müssen ausdrücklich eingetragen werden: `example.com` erlaubt
  nicht automatisch `team.example.com`.
- URLs, Wildcards und vollständige E-Mail-Adressen sind keine gültigen
  Konfigurationseinträge. Internationale Domains als ASCII/Punycode eintragen.
- **Die Liste ist anfangs leer.** Ohne gültige Konfiguration oder bei einem
  ungültigen Eintrag werden Registrierungen mit HTTP 503 abgewiesen. Erst die
  gewünschten Domains speichern, dann das Plugin aktivieren.
- Die Regel gilt für alle Shops, die das aktive Plugin-Set verwenden.
- Zum Abschalten das Plugin deaktivieren und das Set erneut bereitstellen.

Bei `example.com, example.org` gilt beispielsweise:

| E-Mail-Adresse | Ergebnis |
| --- | --- |
| `name@example.com` | erlaubt |
| `name+team@EXAMPLE.ORG` | erlaubt |
| `name@other.example` | abgewiesen |
| `name@sub.example.com` | abgewiesen |
| `name@example.com.evil.test` | abgewiesen |

Abgewiesene Adressen erhalten HTTP 422 und eine Meldung mit den erlaubten
Endungen. Eine gültige Registrierung läuft im vorhandenen Plugin unverändert
weiter. Das Zusatz-Plugin legt selbst keine Kontakte an und versendet keine Mails.

## Unterstützte Registrierungswege

- Standard-LTS-Registrierung: `POST /rest/io/customer`.
- B2B-Shop-Registrierung: `POST /rest/b2b/customer` mit IO-kompatiblen
  Kontaktdaten. Andere B2B-Plugins oder eigene Endpunkte sind separat zu prüfen.
- Geprüft wird die Login-Adresse in `contact.options` mit `typeId=2` und
  `subTypeId=4`, gegebenenfalls zusätzlich der gleichlautende Alias
  `contact.email`. Widersprüchliche Adressen und doppelte Login-Optionen werden
  abgewiesen. Die Rechnungsadresse bestimmt die Freigabe nicht.

Login, Passwort-Zurücksetzen, Gastbestellungen, bestehende Konten und
administrative Kontaktanlage werden durch diesen Filter nicht geändert.
E-Mail-Bestätigung, Nachweis des Postfachbesitzes und Kundenklassenwechsel sind
nicht Bestandteil dieses Plugins.

## Prüfung und technische Grenzen

Die lokale Testsuite prüft Domainlisten, ungültige Konfiguration, Kontaktformate,
Umgehungsversuche und beide Endpunkte. HTTP-Tests führen die tatsächliche
Middleware mit lokalen Ersatzklassen für die Plenty-Schnittstellen aus und
prüfen, dass eine Ablehnung den nachfolgenden Controller nicht erreicht.

Der vollständige Plenty-Build und die Formularprüfung in einer konkreten
Plugin-Kombination stehen noch aus. Vor dem Live-Einsatz im Test-Set prüfen:

1. Fremde Domain in beiden Registrierungswegen absenden: Fehlermeldung, kein angelegter Kontakt.
2. Jede erlaubte Domain mit einer dafür vorgesehenen Testadresse prüfen.
3. Leere Liste und fehlerhafte Domain nur im Test-Set prüfen: keine Registrierung möglich.
4. Bestehende Anmeldung und Checkout prüfen.

Das Plugin registriert eine globale Middleware und greift nur bei den genannten
Registrierungs-POSTs ein. Da `Middleware::before()` keinen dokumentierten
Rückgabewert zum Abbrechen bietet, beendet die Ablehnung die Anfrage nach der
JSON-Fehlerantwort ausdrücklich. Es werden keine Marketplace-Dateien ersetzt,
keine neuen Routen angelegt und keine Datenbankmigrationen ausgeführt.

## Schnittstellen

- [Plenty Plugin-Konfiguration](https://developers.plentymarkets.com/en-gb/developers/main/plugin-configuration/how-to-plugin-configuration.html)
- [Plenty Middleware](https://developers.plentymarkets.com/en-gb/interface/stable7/Miscellaneous.html)
- [IO CustomerResource](https://github.com/plentymarkets/plugin-io/blob/stable/src/Api/Resources/CustomerResource.php)
- [IO AbstractGuard](https://github.com/plentymarkets/plugin-io/blob/stable/src/Guards/AbstractGuard.php)
- [LTS-Registrierungsformular](https://github.com/plentymarkets/plugin-ceres/blob/stable/resources/js/src/app/components/customer/Registration.vue)
