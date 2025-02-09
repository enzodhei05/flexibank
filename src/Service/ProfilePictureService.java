@Service
public class ProfilePictureService {

    private static final String UPLOAD_DIR = "/path/to/upload/dir/";

    public String uploadProfilePicture(MultipartFile file) {
        String fileName = UUID.randomUUID().toString() + "." + FilenameUtils.getExtension(file.getOriginalFilename());
        Path path = Paths.get(UPLOAD_DIR, fileName);
        try {
            Files.write(path, file.getBytes());
        } catch (IOException e) {
            throw new RuntimeException("Failed to upload profile picture", e);
        }
        return fileName;
    }
}
