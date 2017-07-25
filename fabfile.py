# from __future__ import with_statement
from getpass import getpass
from fabric.api import local, abort, settings, run, env, cd, sudo
from fabric.contrib.console import confirm

env.roledefs = {
    'test': ['localhost'],
    'prod': ['tm30@192.168.0.115']
}

env.roledefs['all'] = [h for r in env.roledefs.values() for h in r]


def commit(message='updating...'):
    local("git add --all")
    with settings(warn_only=True):
        result = local("git commit -m '%s'" % message, capture=True)
        if result.failed and not confirm("Tests failed. Continue anyway?"):
            abort("Aborting at your behest")


def pull():
    """
    update environment
    :return:
    """
    local("git pull")


def update_environs(message='updating...'):
    """
    update local working environment
    :return:
    """
    commit(message)
    local("git pull")


def update_prod(message='updating...'):
    """
    update local working environment
    :return:
    """
    with settings(warn_only=True, password='kaadie.com'):
        with cd('/opt/IVR'):
            run("git add --all")
            result = run("git commit -m '%s'" % message, warn_only=True)
            if result.failed and not confirm("Tests failed. Continue anyway?"):
                abort("Aborting at your behest")
            run("git pull")


def push(message='updating...'):
    """
    push changes
    :return:
    """
    commit(message)
    local("git push")


def start_services(service_paths=list()):
    """
    restart a system service
    :param service_paths:
    :return:
    """
    for service_path in service_paths:
        sudo('%s start' % service_path)


def stop_service(service_paths=list()):
    """
    restart a system service
    :param service_paths:
    :return:
    """
    for service_path in service_paths:
        sudo('%s stop' % service_path)


def restart_service(service_paths=list()):
    """
    restart a system service
    :param service_path:
    :return:
    """
    for service_path in service_paths:
        sudo('%s restart' % service_path)


def deploy():
    """
    update production environment
    :return:
    """
    with cd('/opt/IVR'):
        sudo('git pull')
