# Git Credential Manager on Windows

Installing Git Credential Manager (GCM) on Windows stores and reuses your GitHub/remote credentials securely, eliminating repeated prompts and warnings.

## Install

If you installed Git for Windows recently, GCM may already be included. Otherwise:

1. Download the latest installer: https://github.com/GitCredentialManager/git-credential-manager/releases
2. Run the installer (`GCMW-Setup.exe`)
3. Ensure Git is configured to use it:

```powershell
# Verify and set helper
git config --global credential.helper manager-core

# Optional: Store for specific host if needed
# git config --global credential.https://github.com.helper manager-core
```

## Usage

- The first time you `git push` or `git fetch` with a remote, a Windows UI will prompt to sign in
- Choose your provider (e.g., GitHub), then complete OAuth/device flow
- Credentials are securely stored in Windows Credential Manager

## Troubleshooting

- Reset a bad token:

```powershell
cmdkey /list | findstr /i git
# then remove the relevant entry, e.g.
cmdkey /delete:git:https://github.com
```

- Re-run the sign-in by performing a Git operation again (e.g., `git fetch`)

## References

- Docs: https://aka.ms/gcm
- Repo: https://github.com/GitCredentialManager/git-credential-manager
