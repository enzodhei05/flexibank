@Service
public class UserService {

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private ProfilePictureService profilePictureService;

    public User getUserProfile(User user) {
        return userRepository.findById(user.getId()).orElseThrow(() -> new RuntimeException("User not found"));
    }

    public void updateProfilePicture(User user, MultipartFile file) {
        if (!file.isEmpty()) {
            String fileName = profilePictureService.uploadProfilePicture(file);
            user.setProfilePicture(fileName);
            userRepository.save(user);
        }
    }

    public void deleteAccount(User user) {
        userRepository.delete(user);
    }
}
