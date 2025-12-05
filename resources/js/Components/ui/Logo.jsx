export default function Logo({ width = 40, alt = 'Logo', className = '' }) {
  return (
    <img src="/assets/logo.webp" alt={alt} style={{ width, height: 'auto' }} className={className} />
  );
}
