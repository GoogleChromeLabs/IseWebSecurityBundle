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

Due to a lack of Symfony Flex recipe to do so automatically. In your projects `/config/packages` folder, create `ise_web_security.yaml` and populate it with the yaml config detailed below. 

## Config

More Config details can be found [here](https://github.com/GoogleChromeLabs/IseWebSecurityBundle/wiki/Configuration)

>WIP, Config will change over time

The config within your Symfony project will control how the bundle works in your Application.
Below, you will find an example config for the current state of the project that will activate
the majority of the features. The `ise_web_security.yaml.dist` is also an example of this file. 

>ise_web_security.yaml

```yaml
ise_web_security:
    defaults: 
        preset: 'full'
    paths:
        '^/public':
            coop:
                active: false
            coep:
                active: false
            fetch_metadata:
                active: false
        '^/admin':
            fetch_metadata:
                allowed_endpoints: ['/images']
            trusted_types:
                active: true
                polices: ['foo', 'bar']
                require_for: ['script', 'style']

```

## Wiki

This Repo has a wiki! Check it out [here](https://github.com/GoogleChromeLabs/IseWebSecurityBundle/wiki)

## 🤝 Contributing

Issues and pull requests are always welcome. For details, see
[docs/contributing.md](docs/contributing.md)

This is not an officially supported Google product.

