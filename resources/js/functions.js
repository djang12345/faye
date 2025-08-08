export function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = date.toLocaleDateString(undefined, options);

    return formattedDate;
}

export function formatTime(timeString) {
    const [hour, minute] = timeString.split(":").map(Number);
    
    const ampm = hour >= 12 ? "PM" : "AM";
    const formattedHour = hour % 12 || 12; 

    return `${formattedHour}:${String(minute).padStart(2, '0')} ${ampm}`;
}

export function formatMonth(month) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    
    return monthNames[month - 1]; 
}