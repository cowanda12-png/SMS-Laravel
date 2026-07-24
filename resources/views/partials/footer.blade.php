<footer style="background: #1a1a1a; color: rgba(255,255,255,0.7); padding: 1.5rem 0; border-top: 1px solid #2a2a2a; margin-top: auto; position: relative; z-index: 1;">
    <div class="container-fluid px-4">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <span style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">
                    &copy; {{ date('Y') }} Student Management System
                </span>
                <span style="color: rgba(255,255,255,0.15);">|</span>
                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.3);">
                    <i class="fas fa-code me-1"></i> v2.0.1
                </span>
            </div>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <a href="#" style="color: rgba(255,255,255,0.35); text-decoration: none; font-size: 0.8rem; transition: all 0.25s ease; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-shield-alt" style="font-size: 0.7rem;"></i>
                    Privacy
                </a>
                <span style="color: rgba(255,255,255,0.1);">|</span>
                <a href="#" style="color: rgba(255,255,255,0.35); text-decoration: none; font-size: 0.8rem; transition: all 0.25s ease; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-file-contract" style="font-size: 0.7rem;"></i>
                    Terms
                </a>
                <span style="color: rgba(255,255,255,0.1);">|</span>
                <a href="#" style="color: rgba(255,255,255,0.35); text-decoration: none; font-size: 0.8rem; transition: all 0.25s ease; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-headset" style="font-size: 0.7rem;"></i>
                    Support
                </a>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div style="margin-top: 0.8rem; padding-top: 0.8rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <span style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">
                <i class="far fa-clock me-1"></i> {{ now()->format('F d, Y') }}
            </span>
            <div style="display: flex; gap: 1rem;">
                <span style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">
                    <i class="fas fa-server me-1"></i> Status: <span style="color: #28a745;">Operational</span>
                </span>
                <span style="font-size: 0.7rem; color: rgba(255,255,255,0.15);">|</span>
                <span style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">
                    <i class="fas fa-heart me-1" style="color: #dc3545; font-size: 0.6rem;"></i> Made with care
                </span>
            </div>
        </div>
    </div>
</footer>

<style>
    footer a:hover {
        color: rgba(255,255,255,0.8) !important;
        text-decoration: none !important;
    }
    
    @media (max-width: 768px) {
        footer .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        footer > div > div {
            flex-direction: column !important;
            text-align: center !important;
        }
        footer .d-flex {
            justify-content: center !important;
        }
    }
</style>