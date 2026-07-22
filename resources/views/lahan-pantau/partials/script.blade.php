<script>
function lahanPantauPage() {
    return {
        loading: true,
        showModal: false,
        editMode: false,
        submitting: false,
        lahans: [],
        formData: {
            nama_lahan: '',
            lokasi: '',
            deskripsi: '',
            image_url: ''
        },
        editingId: null,

        async init() {
            await this.fetchLahans();
        },

        async fetchLahans() {
            this.loading = true;
            try {
                const response = await fetch('/api/v1/lahan-pantau', {
                    headers: {
                        'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to fetch');
                
                const data = await response.json();
                this.lahans = data.data || [];
            } catch (error) {
                console.error('Error fetching lahans:', error);
                this.showNotification('Gagal memuat data lahan', 'error');
            } finally {
                this.loading = false;
            }
        },

        openCreateModal() {
            this.editMode = false;
            this.editingId = null;
            this.formData = {
                nama_lahan: '',
                lokasi: '',
                deskripsi: '',
                image_url: ''
            };
            this.showModal = true;
        },

        openEditModal(lahan) {
            this.editMode = true;
            this.editingId = lahan.id;
            this.formData = {
                nama_lahan: lahan.nama_lahan,
                lokasi: lahan.lokasi || '',
                deskripsi: lahan.deskripsi || '',
                image_url: lahan.image_url || ''
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editMode = false;
            this.editingId = null;
        },

        async submitForm() {
            if (this.submitting) return;
            
            this.submitting = true;
            
            try {
                const url = this.editMode 
                    ? `/api/v1/lahan-pantau/${this.editingId}`
                    : '/api/v1/lahan-pantau';
                
                const method = this.editMode ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification(
                        this.editMode ? 'Lahan berhasil diupdate' : 'Lahan berhasil ditambahkan', 
                        'success'
                    );
                    this.closeModal();
                    await this.fetchLahans();
                } else {
                    throw new Error(data.message || 'Failed to save');
                }
            } catch (error) {
                console.error('Error saving lahan:', error);
                this.showNotification('Gagal menyimpan data', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async deleteLahan(id, nama) {
            if (!confirm(`Hapus lahan "${nama}"?\n\nPerangkat yang terhubung akan di-unassign.`)) {
                return;
            }

            try {
                const response = await fetch(`/api/v1/lahan-pantau/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification('Lahan berhasil dihapus', 'success');
                    await this.fetchLahans();
                } else {
                    throw new Error(data.message || 'Failed to delete');
                }
            } catch (error) {
                console.error('Error deleting lahan:', error);
                this.showNotification('Gagal menghapus lahan', 'error');
            }
        },

        viewDetail(id) {
            window.location.href = `/lahan-pantau/${id}`;
        },

        showNotification(message, type = 'info') {
            // Simple notification - bisa diganti dengan toast library
            alert(message);
        }
    };
}
</script>
