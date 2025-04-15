function handleNovaError(error, fallback = 'Erro inesperado.') {
    const res = error.response;
    if (res?.status === 422 && res?.data?.errors) {
        const firstError = Object.values(res.data.errors)[0][0];
        Nova.error(firstError);
    } else {
        Nova.error(res?.data?.message || fallback);
    }
}
export default {
    getBackupStatuses() {
        return Nova.request()
            .get('/nova/nova-backup-manager/backup-statuses')
            .then(response => response.data)
            .catch(handleNovaError);
    },

    getBackups(disk) {
        return Nova.request()
            .get(`/nova/nova-backup-manager/backups?disk=${disk}`)
            .then(response => response.data)
            .catch(handleNovaError);
    },

    createBackup() {
        return Nova.request().post(`/nova/nova-backup-manager/backups`).catch(handleNovaError);
    },

    createPartialBackup(option) {
        return Nova.request().post(`/nova/nova-backup-manager/backups`, { option }).catch(handleNovaError);
    },

    deleteBackup({ disk, path }) {
        return Nova.request().delete(`/nova/nova-backup-manager/backups`, {
            params: { disk, path },
        }).catch(handleNovaError);
    },
};
