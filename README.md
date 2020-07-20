[![Build Status](https://travis-ci.com/GoogleChromeLabs/IseWebSecurityBundle.svg?branch=main)](https://travis-ci.com/GoogleChromeLabs/IseWebSecurityBundle)
[![Coverage Status](https://coveralls.io/repos/github/GoogleChromeLabs/IseWebSecurityBundle/badge.svg?branch=ci)](https://coveralls.io/github/GoogleChromeLabs/IseWebSecurityBundle?branch=ci)

# 🔐 IseWebSecurityBundle

A Symfony bundle that implements best practice for security features,
including:

- Content Security Policy (CSP)
- Cross Origin Opener Policy / Cross Origin Embedder Policy (COOP/COEP)
- Fetch metadata headers
- Trusted Types

# 🖥️ Usage

>WIP, package not currently published. 

To install the bundle on a project, add the following lines to your composer.json

```json
"require": {
    "ise/web-security-bundle": "dev-main"
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/GoogleChromeLabs/IseWebSecurityBundle.git"
    }
]
```

## Config

>WIP, Config will change over time

The config within your Symfony project will control how the bundle works in your Application.
Below, you will find an example config for the current state of the project that will activate
the majority of the features.

>ise_web_security.yaml

```yaml
ise_web_security:
    coop:
        active: true
    fetch_metadata:
        active: true
```

## 🤝 Contributing

Issues and pull requests are always welcome. For details, see
[docs/contributing.md](docs/contributing.md)

This is not an officially supported Google product.

