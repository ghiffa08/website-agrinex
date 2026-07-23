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
            device_ids: []
        },
        availableDevices: window.allDevices || [],
        editingId: null,
        sidebarOpen: false, // Add this for sidebar component

        async init() {
            await this.fetchLahans();
        },

        async fetchLahans() {
            this.loading = true;
            try {
                const response = await fetch('/api/v1/lahan-pantau', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                console.log('Fetch response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Fetch error:', errorText);
                    throw new Error('Failed to fetch');
                }
                
                const data = await response.json();
                console.log('Fetched data:', data);
                this.lahans = data.data || [];
                console.log('Lahans array:', this.lahans);
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
                device_ids: []
            };
            this.showModal = true;
            console.log('Modal opened, showModal:', this.showModal);
        },

        openEditModal(lahan) {
            this.editMode = true;
            this.editingId = lahan.id;
            this.formData = {
                nama_lahan: lahan.nama_lahan,
                lokasi: lahan.lokasi || '',
                deskripsi: lahan.deskripsi || '',
                device_ids: lahan.device_ids || []
            };
            this.showModal = true;
            console.log('Edit modal opened, lahan:', lahan);
        },

        closeModal() {
            this.showModal = false;
            this.editMode = false;
            this.editingId = null;
        },

        async submitForm() {
            if (this.submitting) return;
            
            // Validate required field
            if (!this.formData.nama_lahan || this.formData.nama_lahan.trim() === '') {
                this.showNotification('Nama lahan harus diisi', 'error');
                return;
            }
            
            this.submitting = true;
            
            try {
                const url = this.editMode 
                    ? `/api/v1/lahan-pantau/${this.editingId}`
                    : '/api/v1/lahan-pantau';
                
                const method = this.editMode ? 'PUT' : 'POST';
                
                console.log('Submitting:', method, url, this.formData);
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(this.formData)
                });

                console.log('Submit response status:', response.status);
                const data = await response.json();
                console.log('Submit response data:', data);

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
                this.showNotification('Gagal menyimpan data: ' + error.message, 'error');
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
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
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
                this.showNotification('Gagal menghapus lahan: ' + error.message, 'error');
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
