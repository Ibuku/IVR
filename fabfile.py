# from __future__ import with_statement
from getpass import getpass
from fabric.api import local, abort, settings, run, env, cd, sudo
from fabric.contrib.console import confirm

env.roledefs = {
    'test': ['localhost'],
    'prod': ['tm30@192.168.0.116']
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


def update_environs():
    """
    update local working environment
    :return:
    """
    commit()
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


def push():
    """
    push changes
    :return:
    """
    commit()
    local("git push")


def start_service(service_path):
    """
    restart a system service
    :param service_path:
    :return:
    """
    sudo('%s start' % service_path)


def stop_service(service_path):
    """
    restart a system service
    :param service_path:
    :return:
    """
    sudo('%s stop' % service_path)


def restart_service(service_path):
    """
    restart a system service
    :param service_path:
    :return:
    """
    sudo('%s restart' % service_path)


def deploy():
    """
    update production environment
    :return:
    """
    with cd('/opt/IVR'):
        run('git pull')
        restart_service('/etc/init.d/apache2')
        restart_service('/etc/init.d/amportal')
        restart_service('/etc/init.d/elasticsearch')
        restart_service('/etc/init.d/rabbitmq-server')
        restart_service('/etc/init.d/redis-server')
        restart_service('/etc/init.d/node-worker')

